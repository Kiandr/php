<?php

/**
 * Module_FeaturedListings
 */
class Module_FeaturedListings
{

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var int[]
     */
    protected $exclude = [];

    /**
     * @param DB $db
     * @param Settings $settings
     */
    public function __construct(DB $db, Settings $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * Get featured listing
     * @return array|NULL
     */
    public function getResult()
    {
        $results = $this->getResults(1);
        return array_shift($results);
    }

    /**
     * Get featured listings
     * @param int|NULL $limit
     * @return array
     */
    public function getResults($limit = null)
    {
        $results = [];
        $query = $this->getListingsQuery();
        foreach ($query->fetchAll() as $featured) {
            $this->exclude[] = (int) $featured['id'];
            $idx = Util_IDX::getIdx($featured['idx']);
            $db_idx = Util_IDX::getDatabase($featured['idx']);
            $listing = $this->getListingData($idx, $db_idx, $featured['mls_number']);
            if (!empty($listing)) {
                $listing = Util_IDX::parseListing($idx, $db_idx, $listing);
                if (!empty($listing)) {
                    $results[] = $listing;
                }
                if (count($results) >= $limit) {
                    break;
                }
            }
        }
        return $results;
    }

    /**
     * Get IDX feeds
     * @return string[]
     */
    protected function getListingFeeds()
    {
        if (!empty($this->settings->IDX_FEEDS)) {
            return array_keys($this->settings->IDX_FEEDS);
        } else if (!empty($this->settings->IDX_FEED)) {
            return [$this->settings->IDX_FEED];
        }
        return [];
    }

    /**
     * Get IDX listing fields
     * @return array
     */
    protected function getListingFields()
    {
        $fields = Lang::$lang['IDX_LISTING_TAGS'] ?: [];
        return array_merge([
            'ListingMLS',
            'ListingType',
            'ListingPrice',
            'ListingRemarks',
            'ListingOffice',
            'ListingOfficeID',
            'ListingAgent',
            'ListingAgentID',
            'ListingImage',
            'AddressCity',
            'Address'
        ], $fields);
    }

    /**
     * @param int|NULL $limit
     * @return PDOStatement
     */
    protected function getListingsQuery($limit = null)
    {
        $idxFeeds = $this->getListingFeeds();
        $queryString = sprintf(
            "SELECT `id`, `idx`, `mls_number` FROM `featured_listings` WHERE%s `idx` IN (%s) ORDER BY RAND() %s;",
            $this->exclude ? sprintf('`id` NOT IN (%s) AND ', implode(', ', array_fill(0, count($this->exclude), '?'))) : '',
            implode(', ', array_fill(0, count($idxFeeds), '?')),
            is_numeric($limit) ? 'LIMIT ' . $limit : ''
        );
        $queryParams = array_merge($this->exclude, $idxFeeds);
        $query = $this->db->prepare($queryString);
        $query->execute($queryParams);
        return $query;
    }

    /**
     * @param IDX $idx
     * @param Database_MySQLImproved $db_idx
     * @param string $mls
     * @return array
     */
    protected function getListingData($idx, $db_idx, $mls)
    {
        return $db_idx->fetchQuery(sprintf(
            "SELECT SQL_CACHE %s FROM `%s` WHERE `%s` = '%s' LIMIT 1;",
            $idx->selectColumns(null, $this->getListingFields()),
            $idx->getTable(),
            $idx->field('ListingMLS'),
            $db_idx->cleanInput($mls)
        ));
    }
}
