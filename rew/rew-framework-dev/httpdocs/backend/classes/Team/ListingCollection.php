<?php

namespace REW\Backend\Team;

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Util\IDXInterface;

class ListingCollection
{
    /**
     * @var \Backend_Team
     */
    private $team;

    /**
     * @var DBInterface
     */
    private $db;

    /**
     * @var AgentCollection
     */
    private $agentCollection;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var IDXFactoryInterface
     */
    private $idxFactory;

    /**
     * @var bool
     */
    private $useCache = false;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var bool
     */
    private $loadFeaturedAgents = false;

    /**
     * AgentCollection constructor.
     * @param \Backend_Team $team
     * @param DBInterface $db
     * @param AgentCollection $agentCollection
     * @param SettingsInterface $settings
     * @param IDXFactoryInterface $idxFactory
     * @param IDXInterface $idx
     */
    public function __construct(
        \Backend_Team $team,
        DBInterface $db,
        AgentCollection $agentCollection,
        SettingsInterface $settings,
        IDXFactoryInterface $idxFactory,
        IDXInterface $idx
    ) {
        $this->team = $team;
        $this->db = $db;
        $this->agentCollection = $agentCollection;
        $this->settings = $settings;
        $this->idxFactory = $idxFactory;
        $this->idx = $idx;
    }

    /**
     * Sets this collection to cache in memory so that multiple operations can be done without repeating db operations
     * @return $this
     */
    public function cache()
    {
        $this->useCache = true;
        return $this;
    }

    /**
     * Sets a flag to ensure featured agents are loaded.
     * @return $this
     */
    public function loadFeaturedAgents()
    {
        $this->loadFeaturedAgents = true;
        return $this;
    }

    /**
     * Counts the number of CMS listings associated with this team
     * @return int
     */
    public function countCms()
    {
        if ($this->useCache && isset($this->cache[__FUNCTION__])) {
            return $this->cache[__FUNCTION__];
        }

        $agents = $this->agentCollection->getAllAgents();
        if (!$agents) {
            return 0;
        }

        $sql = "SELECT COUNT(`id`) AS `total` FROM `" . TABLE_LISTINGS . "`"
            . " WHERE `agent` IN (" . implode(', ', array_fill(0, count($agents), '?')) . ")"
            . " AND `team` = ?;";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array_merge(array_values($agents), [$this->team->getId()]));

        $cmsListingsCount = (int)$stmt->fetchColumn();

        if ($this->useCache) {
            $this->cache[__FUNCTION__] = $cmsListingsCount;
        }

        return $cmsListingsCount;
    }

    /**
     * Get CMS listing results for this team
     * @param int $pageLimit
     * @param int $offset
     * @return array
     */
    public function getCmsResults($pageLimit, $offset)
    {
        $sqlLimit = '';
        if (($cmsCount = $this->countCms()) > $pageLimit) {
            // SQL Limit
            if ($cmsCount > $pageLimit) {
                $sqlLimit = " LIMIT " . ((int) $pageLimit) . " OFFSET " . ((int) $offset);
            }
        }
        $agents = $this->agentCollection->getAllAgents();
        if (!$agents) {
            return [];
        }

        // Query Agents
        $stmt = $this->db->prepare(
            "SELECT `tl`.*, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name`"
            . ", `a`.`first_name`, `a`.`last_name`, `a`.`image` AS `agent_photo`"
            . " FROM `" . TABLE_LISTINGS . "` `tl`"
            . " JOIN `" . LM_TABLE_AGENTS . "` `a` ON `tl`.`agent` = `a`.`id`"
            . " WHERE `tl`.`agent` IN (" . implode(', ', array_fill(0, count($agents), '?')) . ")"
            . " AND `tl`.`team` = ?"
            . " ORDER BY `price` DESC"
            . $sqlLimit . ";"
        );

        $stmt->execute(array_merge(array_values($agents), [$this->team->getId()]));
        $cmsListings = $stmt->fetchAll();

        // Locate Listing Image
        $imageStmt = $this->db->prepare(
            "SELECT `file` FROM `" . $this->settings['TABLES']['UPLOADS'] . "` WHERE `type` = 'listing'"
            . " AND `row` = :row AND `file` != '' ORDER BY `order` ASC LIMIT 1;"
        );

        $listings = [];
        foreach ($cmsListings as $cmsListing) {
            $imageStmt->execute(['row' => $cmsListing['id']]);
            if ($file = $imageStmt->fetchColumn()) {
                $cmsListing['image'] = $file;
            }
            if (isset($cmsListing['link'])) {
                $cmsListing['link'] = sprintf(
                    URL_LISTING,
                    (!empty($cmsListing['link']) ? $cmsListing['link'] : $cmsListing['id'])
                );
            }
            if (isset($cmsListing['image'])) {
                $cmsListing['image'] = "/thumbs/60x60/uploads/" . rawurlencode($cmsListing['image']);
            }
            $cmsListing['feed'] = 'cms';

            if ($this->loadFeaturedAgents) {
                $cmsListing = $this->loadFeaturedAgentsForListing($cmsListing);
            }

            $listings[] = $cmsListing;
        }

        return $listings;
    }

    /**
     * Count IDX listings for agents in this team for each feed and return an array
     * @return array
     */
    public function countIdx()
    {
        if ($this->useCache && isset($this->cache[__FUNCTION__])) {
            return $this->cache[__FUNCTION__];
        }

        $counts = [];
        foreach ($this->getIdxPartialQueries() as $feed => $partialQuery) {
            // Get idx feed
            $this->idxFactory->switchFeed($feed);
            $idx = $this->idxFactory->getIdx();
            $dbIdx = $this->idxFactory->getDatabase();

            $sql = sprintf($partialQuery, "SQL_CACHE COUNT(`" . $idx->field('ListingMLS') . "`) AS `total`");

            $result = $dbIdx->query($sql);
            $counts[$feed] = $dbIdx->fetchArray($result)['total'];
        }

        $this->cache[__FUNCTION__] = $counts;

        return $counts;
    }

    /**
     * Fetch IDX listings for the agents in this team.
     * @param string $feed
     * @param int $pageLimit
     * @param int $offset
     * @return array
     */
    public function getIdxResults($feed, $pageLimit, $offset)
    {
        if ($this->useCache && isset($this->cache[__FUNCTION__])) {
            return $this->cache[__FUNCTION__];
        }

        $partialQueries = $this->getIdxPartialQueries();
        $agentIdxIds = $this->agentCollection->getAllIdxAgentIds();

        // Get idx feed
        $this->idxFactory->switchFeed($feed);
        $idx = $this->idxFactory->getIdx($feed);
        $dbIdx = $this->idxFactory->getDatabase($feed);

        // Get Feed Ids & Agents
        $feedIds = array_filter(array_column($agentIdxIds[$feed], 'idx_id'));
        $feedAgents = [];
        foreach ($agentIdxIds[$feed] as $feedId) {
            $feedAgents[strtolower($feedId['idx_id'])] = [
                'id' => $feedId['id'],
                'name' => $feedId['name']
            ];
        }

        $sqlLimit = " LIMIT " . ((int)$pageLimit) . " OFFSET " . ((int)$offset);

        // Query Agents
        $sql = sprintf($partialQueries[$feed], $idx->selectColumns('`t1`.'))
            . " ORDER BY `t1`.`" . $idx->field('ListingPrice') . "` DESC"
            . $sqlLimit;
        $result = $dbIdx->query($sql);

        $listings = [];
        while ($idxListing = $dbIdx->fetchArray($result)) {
            $idxListing = $this->idx->parseListing($idxListing);
            $listing = [
                'id' => $idxListing['ListingMLS'],
                'title' => $idxListing['Address'] . ' (' . \Lang::write('MLS_NUMBER') . $idxListing['ListingMLS'] . ')',
                'feed' => $feed,
                'price' => $idxListing['ListingPrice'],
                'address' => $idxListing['Address'],
                'city' => $idxListing['AddressCity'],
                'state' => $idxListing['AddressState'],
                'image' => \IDX_Feed::thumbUrl($idxListing['ListingImage'], \IDX_Feed::IMAGE_SIZE_SMALL),
                'link' => $idxListing['url_details'],
                'agent' => $feedAgents[$idxListing['ListingAgentID']]['id'],
                'agent_name' => $feedAgents[$idxListing['ListingAgentID']]['name']
            ];

            $listings[] = $this->loadFeaturedAgentsForListing($listing);
        }

        if ($this->useCache) {
            $this->cache[__FUNCTION__] = $listings;
        }

        return $listings;
    }

    /**
     * Load featured agents for the provided listing
     * @param array $listing
     * @return array
     */
    public function loadFeaturedAgentsForListing($listing)
    {
        $stmt = $this->db->prepare("SELECT `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`"
            . ", `a`.`first_name`, `a`.`last_name`, `a`.`image`"
            . " FROM `" . TABLE_TEAM_LISTINGS . "` `tf`"
            . " JOIN `" . LM_TABLE_AGENTS . "` `a` ON `tf`.`agent_id` = `a`.`id`"
            . " WHERE `tf`.`agent_id` != :agent_id AND `tf`.`team_id` = :team_id AND `listing_id` = :listing_id AND `listing_feed` = :listing_feed"
            . " ORDER BY `order`;");
        $stmt->execute([
            'agent_id' => $listing['agent'],
            'team_id' => $this->team->getId(),
            'listing_id' => $listing['id'],
            'listing_feed' => $listing['feed']
        ]);
        $featuredAgents = $stmt->fetchAll();
        $listing['featured_agents'] = $featuredAgents;

        return $listing;
    }

    /**
     * Get available feeds
     * @return array
     */
    public function getFeeds()
    {
        if (!empty($this->settings['IDX_FEEDS'])) {
            $feeds = array_keys($this->settings['IDX_FEEDS']);
        } else {
            $feeds = [$this->settings['IDX_FEED']];
        }

        return $feeds;
    }

    /**
     * Builds partial IDX queries and returns an array (one per feed)
     * @return array
     */
    public function getIdxPartialQueries()
    {
        if ($this->useCache && isset($this->cache[__FUNCTION__])) {
            return $this->cache[__FUNCTION__];
        }

        $agentIdxIds = $this->agentCollection->getAllIdxAgentIds();

        // Get Feeds Array
        $feeds = $this->getFeeds();
        $partialQueries = [];

        // Get Agent Leads on Each Feed
        foreach ($feeds as $feed) {
            if (!empty($agentIdxIds[$feed])) {
                // Get Feed Ids
                $feedIds = array_filter(array_column($agentIdxIds[$feed], 'idx_id'));

                // Get idx feed
                $idx = $this->idxFactory->getIdx();
                $dbIdx = $this->idxFactory->getDatabase();

                $partialQueries[$feed] = "SELECT %s"
                    . " FROM `" . $idx->getTable() . "` `t1`"
                    . " WHERE `t1`.`" . $idx->field('ListingAgentID') . "`"
                    . " IN ('" . implode("', '", array_map([$dbIdx, 'cleanInput'], $feedIds)) . "')";
            }
        }

        if ($this->useCache) {
            $this->cache[__FUNCTION__] = $partialQueries;
        }

        return $partialQueries;
    }
}
