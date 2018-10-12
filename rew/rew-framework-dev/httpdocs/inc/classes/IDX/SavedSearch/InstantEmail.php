<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\IDX\ComplianceInterface as IDXComplianceInterface;
use REW\Backend\View\Interfaces\FactoryInterface;

/**
 * Saved Search Instant Email
 * @package IDX
 */
class IDX_SavedSearch_InstantEmail
{

    /**
     * @var int
     */
    const LISTING_LIMIT = 10;

    /**
     * @var PageInterface
     */
    private $page;

    /**
     * @var SkinInterface
     */
    private $skin;

    /**
     * @var DBInterface
     */
    private $db;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var IDXComplianceInterface
     */
    private $idxCompliance;

    /**
     * @var FactoryInterface
     */
    private $viewFactory;

    /**
     * Saved Search id
     * @var int
     */
    private $savedSearchId;

    /**
     * Agent That Created the Saved Search
     * @var int
     */
    private $createdByAgentId;

    /**
     * Saved Search Data
     * @var array
     */
    private $savedSearchData = [];

    /**
     * Agent Object
     * @var object
     */
    private $agent;

    /**
     * Site Urls
     * @var array
     */
    private $siteUrls = [];

    /**
     * Listing Results
     * @var array
     */
    private $listingResults = [];

    /**
     * Listing Results Count
     * @var int
     */
    private $listingResultsCount;

    /**
     * Listings Markup
     * @var string
     */
    private $listingsMarkup;

    /**
     * Mailer
     * @var object
     */
    private $mailer;

    /**
     * Mailer Tags
     * @var array
     */
    private $tags;

    /**
     * Success Messages accumulated from this class' processes
     * @var array
     */
    private $successMessages = [];

    /**
     * Error Messages accumulated from this class' processes
     * @var array
     */
    private $errorMessages = [];

    /**
     * Send Instant Email for a Saved Search
     * @param PageInterface $page
     * @param SkinInterface $skin
     * @param DBInterface $db
     * @param SettingsInterface $settings
     * @param IDXComplianceInterface $idxCompliance
     * @param FactoryInterface $viewFactory
     */
    public function __construct(
        PageInterface $page,
        SkinInterface $skin,
        DBInterface $db,
        SettingsInterface $settings,
        IDXComplianceInterface $idxCompliance,
        FactoryInterface $viewFactory
    )
    {
        $this->page = $page;
        $this->skin = $skin;
        $this->db = $db;
        $this->settings = $settings;
        $this->idxCompliance = $idxCompliance;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Set Data
     * @param $savedSearchId int
     * @param $createdByAgentId int|null
     * @return void
     */
    public function setData($savedSearchId, $createdByAgentId = null)
    {
        // set params
        $this->savedSearchId = $savedSearchId;
        $this->createdByAgentId = $createdByAgentId;
        // set data
        $this->setSavedSearchData($this->savedSearchId);
        $this->setAgent(!empty($this->createdByAgentId) ? $this->createdByAgentId : $this->savedSearchData['search']['agent']);
        $this->setSiteUrls($this->agent);
        $this->setListingsData($this->savedSearchData['search'], $this->agent, $this->siteUrls);
        $this->setListingsMarkup($this->listingResults, $this->savedSearchData['search']['idx']);
        $this->setEmail($this->savedSearchData, $this->agent, $this->siteUrls, $this->listingResultsCount);
    }

    /**
     * Set Saved Search Data
     * @param $savedSearchId int
     * @return void
     */
    private function setSavedSearchData($savedSearchId)
    {
        $available_feeds = !empty($this->settings->IDX_FEEDS) ? array_keys($this->settings->IDX_FEEDS) :
            [$this->settings->IDX_FEED];

        // Feeds That We Will Check For Listings As Required
        $feeds = [];

        // Generate Email Body (HTML Only)
        ob_start();
        include $this->page->locateTemplate('idx', 'emails', 'saved_searches');
        $default_saved_search_message = ob_get_contents();
        ob_end_clean();

        // Get CMS DB
        $db_users = $this->db->get();

        // Populate Feeds List
        foreach ($available_feeds as $feed) {

            //Add Saved Search Message To Feed Info
            $setting = $db_users->fetch("SELECT idxs.`savedsearches_responsive`,
                                            idxs.`savedsearches_message`,
                                            idxs.`savedsearches_responsive_params`,
                                            idxs.`force_savedsearches_responsive`
                                     FROM `" . TABLE_IDX_SYSTEM . "` idxs
                                     WHERE idx in('" . $feed . "', '')
                                     ORDER BY FIELD(idx, '" . $feed . "', '') LIMIT 1;");
            if ($setting["force_savedsearches_responsive"] == 'true') {
                $setting["savedsearches_responsive"] = 'true';
            }
            if ($setting["savedsearches_responsive"] == 'true') {
                // Unserialize template settings
                $setting["params"] = unserialize($setting["savedsearches_responsive_params"]);
                $setting["params"]["listings"]["hero"] = !empty($this->settings['idx']['saved_searches']['hero_image']);
            }
            $setting['savedsearches_message'] = $setting['savedsearches_message'] ?: $default_saved_search_message;
            $feeds[$feed] = $setting;
        }

        // Get List Of Active Feeds On Website
        $feed_names = array_keys($feeds);

        // Wrap Feed Names In Quotes For Upcoming Query
        array_walk($feed_names, function (&$value) {
            $value =  "'" . $value . "'";
        });
        // Users Searches Query
        $query = "SELECT
            `t1`.`id`,
            `t1`.`user_id`,
            `t1`.`title`,
            `t1`.`criteria`,
            `t1`.`idx`,
            `t1`.`frequency`,
            `t1`.`sent`,
            UNIX_TIMESTAMP(`t1`.`timestamp_sent`) AS `timestamp_sent`,
            `t2`.`first_name`,
            `t2`.`last_name`,
            `t2`.`email`,
            `t2`.`agent`,
            `t2`.`verified`,
            `t2`.`bounced`,
            `t2`.`fbl`,
            `t2`.`guid`,
            `t2`.`email_alt`,
            `t2`.`email_alt_cc_searches`
            FROM `users_searches` `t1`
            JOIN `users` `t2` ON `t1`.`user_id` = `t2`.`id`
            WHERE `t1`.`source_app_id` IS NULL
            AND `t2`.`opt_searches` = 'in'
            AND `t2`.`bounced` != 'true'
            AND `t2`.`fbl` != 'true'
            AND `t1`.`idx` IN (" . implode(',', $feed_names) . ")
            AND `t1`.`id` = '" . $savedSearchId . "'
            ORDER BY `t2`.`agent` ASC";

        // Saved Search Result
        $search = $db_users->fetch($query);

        if (empty($search)) {
            $this->errorMessages[] = 'Your saved search criteria preferences do not allow this action.';
            return;
        }

        // Check If E-Mail Host Is Blocked
        if (Validate::verifyWhitelisted($search['email'])) {
            $this->errorMessages[] = $search['email'] .
                '\'s e-mail provider is on the server block list - skipping automated e-mail';
            return;
        }

        // Check If E-Mail Host Requires Verification
        if (Validate::verifyRequired($search['email']) ||
            !empty($this->settings->SETTINGS['registration_verify'])) {
            if ($search['verified'] != 'yes') {
                $this->errorMessages[] = $search['email'] .
                    '\'s e-mail provider is set to require e-mail verification on this server, 
                    but the account has not been verified yet - skipping e-mail';
                return;
            }
        }

        $uuid = $db_users->fetch("SELECT UUID() UUID;");

        // Get Search Index Tempalte
        $indexTemplate = $this->skin->getSavedSearchEmailPath() . "index.php";

        $this->savedSearchData = [
            'search'        => $search,
            'settings'      => $feeds[$search['idx']],
            'uuid'          => $uuid["UUID"],
            'indexTemplate' => $indexTemplate
        ];
    }

    /**
     * Set Agent
     * @param $agentId int
     * @return void
     */
    private function setAgent($agentId)
    {
        $this->agent = Backend_Agent::load($agentId);
    }

    /**
     * Set Site Urls
     * @param $agent object
     * @return void
     */
    private function setSiteUrls($agent)
    {
        // Agent CMS
        if ($agent['cms'] == 'true' && !empty($agent['cms_link'])) {
            $this->siteUrls['url'] = $this->settings->SETTINGS['URL'];
            $this->siteUrls['url_agent'] = sprintf($this->settings->SETTINGS['URL_AGENT_SITE'], $agent['cms_link']);
            $this->siteUrls['URL'] = $this->siteUrls['url_agent'];
            $this->siteUrls['URL_IDX_SAVED_SEARCH'] =
                str_replace($this->siteUrls['url'], $this->siteUrls['url_agent'], $this->settings->SETTINGS['URL_IDX_SAVED_SEARCH']);
        } else {
            $this->siteUrls['URL_IDX_SAVED_SEARCH'] = $this->settings->SETTINGS['URL_IDX_SAVED_SEARCH'];
            $this->siteUrls['URL'] = $this->settings->SETTINGS['URL'];
        }
    }

    /**
     * Set Listings Data
     * @param search array
     * @param $agent object
     * @param $siteUrls array
     * @return void
     */
    private function setListingsData($search, $agent, $siteUrls)
    {
        try {
            // IDX Objects
            $idx = Util_IDX::getIdx($search['idx']);
            $db_idx = Util_IDX::getDatabase($search['idx']);

            // Error Occurred
        } catch (Exception $e) {
            $this->errorMessages[] = 'Failed to load IDX ' . $search['idx'] . '! Skipping to next search';
        }

        // $_REQUEST Array
        $_REQUEST = array();

        // Search Criteria
        if (!empty($search['criteria'])) {
            // Searchable Fields
            $search_fields = search_fields($idx);
            $search_fields = array_keys($search_fields);

            // Search Criteria
            $criteria = unserialize($search['criteria']);
            if (!empty($criteria) && is_array($criteria)) {
                // Set $_REQUEST
                foreach ($criteria as $key => $val) {
                    if (in_array($key, array_merge(array('map', 'view', 'search_location'), $search_fields))) {
                        if (!isset($_REQUEST[$key])) {
                            $_REQUEST[$key] = $val;
                        }
                    }
                }
            }

            // Snippet, Over-Ride Feed Defaults
            $_REQUEST['search_city'] = isset($_REQUEST['search_city']) ? $_REQUEST['search_city'] : false;
            $_REQUEST['search_type'] = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : false;
        }

        // Build Query
        $search_vars        = $idx->buildWhere($idx, $db_idx, 't1');
        $search_where       = $search_vars['search_where'];
        $search_title       = $search_vars['search_title'];
        $search_criteria    = $search_vars['search_criteria'];

        // Query Collection
        $search_where = !empty($search_where) ? array($search_where) : array();

        // Map Queries

        // HAVING Queries
        $search_having = array();

        // Search Group
        $search_group = array();

        // Latitude / Longitude Columns
        $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
        $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

        // Search In Bounds
        if (!empty($_REQUEST['map']['bounds']) && $this->settings->IDX_FEED != 'cms') {
            $bounds = $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'],
                $search_group, $col_latitude, $col_longitude);
        }

        // Search In Radiuses
        $radiuses =
            $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);

        // Search In Polygons
        $polygons =
            $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
        if (!empty($polygons)) {
            $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
        }

        // Add To Search Criteria
        if (!empty($search_group)) {
            $search_where[] = '(' . implode(' OR ', $search_group) . ')';
        }

        // Query String (WHERE & HAVING)
        $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') .
            (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

        $select_columns = array (
            $idx->field('Address'),
            $idx->field('AddressCity'),
            $idx->field('ListingPrice'),
            $idx->field('ListingImage'),
            $idx->field('ListingMLS'),
            $idx->field('NumberOfBedrooms'),
            $idx->field('NumberOfBathrooms'),
            $idx->field('ListingStatus'),
            $idx->field('ListingRemarks'),
            $idx->field('ListingAgent'),
            $idx->field('ListingOffice'),
            $idx->field('NumberOfSqFt'),
        );

        // New Listings Query
        $query = "SELECT " . $idx->selectColumns("`t1`.", $select_columns);
        $geo_join = '';
        if (!empty($polygons)) {
            $query .= ", `t2`.`Point`";
            $geo_join = " JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`." . $idx->field('ListingMLS') . "=`t2`.`ListingMLS`"
                . " AND `t1`.`" . $idx->field('ListingType') . "`=`t2`.`ListingType`";
        }
        $query .= " FROM `" . $idx->getTable() . "` `t1`" . $geo_join
            . " WHERE " . (!empty($search_where) ? $search_where : '')
            . " ORDER BY `t1`.`timestamp_created` DESC";

        // Query count results
        $this->listingResultsCount = 0;
        if ($count_query = $db_idx->query("SELECT COUNT(*) as count from ($query) ttl;")) {
            $count_query = $db_idx->fetchArray($count_query);
            if (!empty($count_query["count"])) {
                $this->listingResultsCount = $count_query["count"];
            }
        }

        // Limit
        $limit = !empty($this->savedSearchData['settings']['params']['listings']['num_rows']) ?
            $this->savedSearchData['settings']['params']['listings']['num_rows'] * 2 : self::LISTING_LIMIT;

        if (!empty($this->savedSearchData['settings']['params']['listings']['hero'])) {
            $limit += 1;
        }

        // Execute Query
        if ($listings = $db_idx->query($query." LIMIT " . $limit)) {

            // Select Most Recent New Listings Matching Search
            while ($listing = $db_idx->fetchArray($listings)) {
                // Parse Listing
                $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                // Replace Original URL With Agent Site URL If Assigned Agent Has Subdomain
                if ($agent['cms'] == 'true' && !empty($agent['cms_link'])) {
                    $listing['url_details'] = str_replace($siteUrls['url'], $siteUrls['url_agent'], $listing['url_details']);
                }

                // Append UID to Link
                $listing['url_details'] .= '?uid=' . Format::toGuid($search['guid']);

                // Add To Collection
                $results[] = $listing;
            }

            $this->listingResults = $results;

        }
    }

    /**
     * Set Listings Markup
     * @param $results array
     * @param $search_idx string
     * @return void
     */
    private function setListingsMarkup($results, $search_idx)
    {
        try {
            // IDX Objects
            $idx = Util_IDX::getIdx($search_idx);

            // Error Occurred
        } catch (Exception $e) {
            $this->errorMessages[] = 'Failed to load IDX ' . $search_idx . '! Could not generate listings markup.';
        }

        // Generate Results
        if (!empty($results) && is_array($results)) {
            global $_COMPLIANCE;
            ob_start();
            foreach ($results as $result) {
                ?>
                <!-- MLS Listing -->
                <table align="center" width="625" cellpadding="5" cellspacing="0" style="border: 1px solid #ccc; background-color: #fff;">
                    <tr>
                        <td width="200">
                            <a href="<?=$result['url_details']; ?>"><img src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="" width="200" height="150" style="width: 200px; height: 150px" border="0" /></a>
                        </td>
                        <td valign="top">
                            <div style="padding: 20px; font-size: 14px;">
                                <?php if (!empty($_COMPLIANCE['results']['show_icon'])) {
                                    echo '<span style="float: right;">' . $_COMPLIANCE['results']['show_icon'] . '</span>';
                                } ?>
                                <i style="color: #777; font-size: 12px;"><?=Lang::write('MLS_NUMBER'); ?><?=$result['ListingMLS']; ?></i><br />
                                <b>$<?=Format::number($result['ListingPrice']); ?></b><br />
                                <?=$result['NumberOfBedrooms']; ?> Bedrooms, <?=$result['NumberOfBathrooms']; ?> Bathrooms<br />
                                <?=ucwords(strtolower($result['AddressCity'])); ?>, <?=ucwords(strtolower($result['AddressState'])); ?>
                                <?=!empty($_COMPLIANCE['results']['show_status']) ? '<br />Status: ' . $result['ListingStatus'] : ''; ?>
                                <p style="font-size: 12px;"><?=substr(ucwords(strtolower($result['ListingRemarks'])), 0, 100); ?>...</p>
                                <div style="background: #ddd; padding: 3px 8px; margin-top: 15px;">
                                    <a href="<?=$result['url_details']; ?>" style="color: #333; font-family: georgia; font-style: italic; display: block;">Read More &raquo;</a>
                                </div>
                                <?php if (!empty($_COMPLIANCE['results']['lang']['provider_bold'])) { ?>
                                    <?php if (!empty($_COMPLIANCE['results']['show_agent']) || !empty($_COMPLIANCE['results']['show_office'])) { ?>
                                        <p style="font-size: 12px; font-weight: bold;"><?=!empty($_COMPLIANCE['results']['lang']['provider']) ? $_COMPLIANCE['results']['lang']['provider'] : 'Provided by:';?> <?=!empty($_COMPLIANCE['results']['show_agent']) && !empty($result['ListingAgent']) ? $result['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['results']['show_office']) && !empty($result['ListingOffice']) ? (!empty($_COMPLIANCE['results']['show_agent']) ? ', ' : '') . $result['ListingOffice'] : ''; ?></p>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php if (!empty($_COMPLIANCE['results']['show_agent']) || !empty($_COMPLIANCE['results']['show_office'])) { ?>
                                        <p style="font-size: 10px;"><?=!empty($_COMPLIANCE['results']['lang']['provider']) ? $_COMPLIANCE['results']['lang']['provider'] : 'Listing courtesy of';?> <?=!empty($_COMPLIANCE['results']['show_agent']) && !empty($result['ListingAgent']) ? $result['ListingAgent'] : ''; ?><?=!empty($_COMPLIANCE['results']['show_office']) && !empty($result['ListingOffice']) ? (!empty($_COMPLIANCE['results']['show_agent']) && !empty($result['ListingAgent']) ? ', ' : '') . $result['ListingOffice'] : ''; ?></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                </table>
                <?php
            }
            $this->idxCompliance->showDisclaimer(true, $idx->getLink());
            $this->listingsMarkup = ob_get_clean();
        }
    }

    /**
     * Set Email
     * @param $savedSearchData array
     * @param $agent object
     * @param $siteUrls array
     * @param $listingResultsCount int
     * @return void
     */
    private function setEmail($savedSearchData, $agent, $siteUrls, $listingResultsCount)
    {
        // grab saved search and lead data
        $params = $savedSearchData;

        // no saved search data, get out
        if (empty($params)) {
            return;
        }

        // Get CMS DB
        $db = $this->db->get();

        // listing data
        try {
            // Get Params
            try {

                $search = $params['search'];

                $settings = $params["settings"];

                $saved_search_message = $settings['savedsearches_message'];

                $uuid = $params['uuid'];

                $results = $this->listingResults;

                $resultsMarkup = $this->listingsMarkup;

                if ($settings["savedsearches_responsive"] === "true") {

                    // Check if responsive template exists
                    $indexTemplate = $params['indexTemplate'];

                    $view = $this->viewFactory;

                    if (!$view->exists($indexTemplate)) {
                        // Force using legacy template if there is no responsive partials for the skin
                        $settings["savedsearches_responsive"] = "false";
                    } else {
                        switch ($settings["params"]["sender"]["from"]) {
                            case "admin":
                                $sender = Backend_Agent::load(1);
                                $sender["name"] = $sender["first_name"] . " " . $sender["last_name"];
                                break;
                            case "custom":
                                $sender["name"] = $settings["params"]["sender"]["name"];
                                $sender["email"] = $settings["params"]["sender"]["email"];
                                break;
                        }
                    }
                }

                if (!in_array($settings["params"]["sender"]["from"], ['admin', 'custom'])) {
                    $sender = $agent;
                    $sender["name"] = $sender["first_name"] . " " . $sender["last_name"];
                }
            } catch (Exception $e) {
                $this->errorMessages[] = "Unable to get saved search params. Here's why: " . PHP_EOL . $e->getMessage();
            }

            // No Listings, End The Job
            if (empty($results)) {
                $this->errorMessages[] = "Skipping Email to " . $search['email'] . ".  No Listings Found For Search " .
                    $search['id'];
                return;
            }

            // Execute Query
            if (!empty($results)) {

                // Setup Mailer
                $subject = date('M j') . ' - Some new listings from your search ' . $search['title'];
                $mailer = new Backend_Mailer(array(
                    'subject' => $subject
                ));

                // Set Sender
                $mailer->setSender($sender['email'], $sender['name']);

                // Set Recipient
                $mailer->setRecipient($search['email'], $search['first_name'] . ' ' . $search['last_name']);

                // CC Alternate Email if Opted-in
                if (!empty($search['email_alt_cc_searches']) && $search['email_alt_cc_searches'] === 'true') {
                    $mailer->addCC($search['email_alt']);
                }

                // Legacy Saved Searches e-mail
                if ($settings["savedsearches_responsive"] === "false") {

                    // Email Tags
                    $tags = array(
                        'first_name'	=> $search['first_name'],
                        'last_name'		=> $search['last_name'],
                        'email'			=> $search['email'],
                        'agent_name'	=> $agent['name'],
                        'signature'		=> $agent['signature'],
                        'search_url'	=>
                            sprintf($siteUrls['URL_IDX_SAVED_SEARCH'], $search['id']) . '?uid=' . Format::toGuid($search['guid']),
                        'search_title'	=> $search['title'],
                        'results'		=> $resultsMarkup,
                        'unsubscribe'	=> $siteUrls['URL'] . 'unsubscribe/' . Format::toGuid($search['guid']) . '/',
                        'url'			=> $siteUrls['URL'] . '?uid=' . sha1(strtoupper($search['email'])),
                        'domain'		=> $siteUrls['URL']
                    );
                    if ($settings["savedsearches_responsive"] === "true" ) {
                        $tags['result_count'] = $listingResultsCount;
                    }
                    // Using Responsive Template
                } else {

                    // Agent Name
                    $agent['name'] = $agent['first_name'] . ' ' . $agent['last_name'];

                    // Agent Link
                    if (!empty($this->settings->MODULES['REW_AGENT_MANAGER']) && $agent['display'] == 'Y') {
                        $agent['link'] = Settings::getInstance()->SETTINGS['URL'] . 'agents/' . Format::slugify($agent['name']) . '/';
                    }

                    array_walk($results, function($i) {
                        $i['ListingImage'] =  Format::thumbUrl($i['ListingImage'], '540x400');
                    });

                    // Get Mailing address
                    if (!empty($settings["params"]["mailing_address"]["from"])) {
                        switch ($settings["params"]["mailing_address"]["from"]) {
                            case "agent":
                                $office_id = $agent["office"];
                                break;
                            case "admin":
                                $result = $db->fetch("SELECT office
                                                  FROM `agents`
                                                  WHERE id = :id;", ["id" => 1]);
                                $office_id = $result["office"];
                                break;
                            default:
                                $office_id = null;
                        }

                        // Use default office
                        if (empty($office_id)) {
                            $office_id = $settings["params"]["mailing_address"]["office_id"];
                        }

                        if (!empty($office_id)) {
                            $office = $db->fetch("SELECT title, address, city, state, zip
                                              FROM `featured_offices`
                                              WHERE id = :id;", ["id" => $office_id]);
                        }
                    }

                    // Get Social media links
                    $social_media = [];
                    if (!empty($settings["params"]["social_media"]["from"])) {
                        $social_media_agent = Backend_Agent::load(
                            $settings["params"]["social_media"]["from"] == "agent" ? $agent["id"] : 1);
                        $social_media = $social_media_agent->getSocialNetworks();
                    }

                    // Add search result count and search url
                    $search["count"] = $listingResultsCount;
                    $search["url"] = sprintf($siteUrls['URL_IDX_SAVED_SEARCH'], $search['id']) . '?uid=' .
                        Format::toGuid($search['guid']);

                    // Get User Info
                    $user = [
                        'first_name'	=> $search['first_name'],
                        'last_name'		=> $search['last_name'],
                        'email'			=> $search['email']
                    ];

                    // Gather site info
                    $site = [
                        "url"  => $siteUrls['URL'],
                        "name" => $_SERVER['HTTP_HOST']
                    ];

                    $view = $this->viewFactory;

                    $saved_search_message = $view->render(
                        $indexTemplate, [
                        "search" => $search,
                        "permalink" => $siteUrls['URL'] . "email/" . $uuid,
                        "site" => $site,
                        "user" => $user,
                        "listings" => $results,
                        "agent" => $agent,
                        "social_media" => $social_media,
                        "office" => $office,
                        "unsubscribe" => $siteUrls['URL'] . 'unsubscribe/' . Format::toGuid($search['guid']) . '/',
                        "sub_preferences" => $siteUrls['URL'] . 'idx/dashboard.html?view=preferences',
                        "message" => $settings["params"]['message']['body'],
                        "params" => $settings["params"]
                    ]);
                    $tags = ['results' => ''];
                }

                if ($settings["savedsearches_responsive"] === "true") {
                    //Replace tags from Subject
                    $sub_tags = [
                        'date'         => date('M j'),
                        'search_title' => $search['title'],
                        'first_name'   => $search['first_name'],
                        'last_name'    => $search['last_name'],
                        'site_name'    => $_SERVER['HTTP_HOST'],

                    ];
                    $mailer->setSubject($mailer->replaceTags($settings["params"]['message']['subject'], $sub_tags));
                }

                // Set Message
                $mailer->setMessage($saved_search_message);

                // Set Tags
                $this->tags = $tags;

                // Set Mailer
                $this->mailer = $mailer;

            }

        } catch (Exception $e) {
            $this->errorMessages[] = $e->getMessage();
        }

    }

    /**
     * Get Success Messages
     * @return array
     */
    public function getSuccessMessages()
    {
        return $this->successMessages;
    }

    /**
     * Get Error Messages
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Send Email
     * @return void
     */
    public function sendEmail()
    {
        // grab saved search and lead data
        $params = $this->savedSearchData;

        // no saved search data, get out
        if (empty($params)) {
            return;
        }

        $search = $params['search'];

        $tags = $this->tags;

        $mailer = $this->mailer;
        // Send Email
        if ($mailer->Send($tags)) {
            $this->successMessages[] = 'Sent Email To ' . $search['email'] . ' For Search ' .
                $search['title'];

            $this->logSentEmail();

        } else {
            // Mailer Error
            $this->errorMessages[] = 'Failed To Send Email To ' . $search['email'] . ' For Search ' . $search['id'];
        }

    }

    /**
     * Log Sent Email
     * @return void
     */
    private function logSentEmail()
    {
        // grab saved search and lead data
        $params = $this->savedSearchData;

        $search = $params['search'];

        $uuid = $params['uuid'];

        $mailer = $this->mailer;

        // Log Event: Listings Update Sent To Lead
        $event = new History_Event_Email_Listings(array(
            'subject'   => $mailer->getSubject(),
            'message'   => $mailer->getMessage(),
            'tags'      => $mailer->getTags()
        ), array(
            new History_User_Lead($search['user_id'])
        ));

        // Save To DB
        $event_id = $event->save();

        $db_users = $this->db->get();

        // Tie email uuid to event
        $db_users->prepare(
            "INSERT INTO  `users_emails` SET `guid` = GuidToBinary(:uuid), `event_id` = :event_id;"
        )->execute(["uuid" => $uuid, "event_id" => $event_id]);
    }
}
