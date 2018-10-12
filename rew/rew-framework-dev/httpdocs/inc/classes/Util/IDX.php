<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Util\IDXInterface as UtilIDXInterface;

/**
 * Util_IDX is a utility class containing methods used in our IDX Platform
 *
 */
class Util_IDX implements UtilIDXInterface
{

    /**
     * @var IDXFactoryInterface
     */
    private $idxFactory;

    /**
     * @var DBFactoryInterface
     */
    private $dbFactory;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var PageInterface
     */
    private $page;

    /**
     * @var HooksInterface
     */
    private $hooks;

    /**
     * Util_IDX constructor.
     * @param SettingsInterface $settings
     * @param IDXFactoryInterface $idxFactory
     * @param DBFactoryInterface $dbFactory
     * @param PageInterface $page
     * @param HooksInterface $hooks
     */
    public function __construct(
        SettingsInterface $settings,
        IDXFactoryInterface $idxFactory,
        DBFactoryInterface $dbFactory,
        PageInterface $page,
        HooksInterface $hooks
    ) {
    
        $this->idxFactory = $idxFactory;
        $this->dbFactory = $dbFactory;
        $this->settings = $settings;
        $this->page = $page;
        $this->hooks = $hooks;
    }

    /**
     * Get Next/Previous Pagination for Listing Result
     *  - This code does not work when using polygon searches due to the missing join of the geocodes table. This is intentional as polygon searches are extremely slow.
     * @param string $mls_number
     * @param string $search_query
     * @param boolean $bounds
     * @return array[prev,next]
     */
    public function paginateListing($mls_number, $search_query, $bounds = false)
    {
        if (!$this instanceof self) {
            $args = func_get_args();
            return Container::getInstance()->get(UtilIDXInterface::class)->paginateListing($args[2], $args[3], $args[4]);
        }
        $timer = Profile::timer()->stopwatch(__METHOD__)->start();
        // Listing Pagination
        $paginate = array();
        if (!empty($search_query)) {
            $idx = $this->idxFactory->getIdx();
            $dbIdx = $this->idxFactory->getDatabase();

            // Get WHERE and ORDER BY Queries
            $explode_where = explode('WHERE', $search_query);
            $explode_order = explode('ORDER BY', $search_query);

            // Remove end of subquery from WHERE
            if (($pos = strpos($explode_where[1], "ORDER BY")) !== false) {
                $explode_where[1] = substr($explode_where[1], 0, $pos);
            }

            $pos = strpos($explode_order[1], "LIMIT");
            // Check whether there was actually a LIMIT set in the query and remove from ORDER BY if so
            if ($pos > 0) {
                $explode_order[1] = substr($explode_order[1], 0, $pos);
            }

            // Use WHERE or ORDER BY
            $where = ($explode_where[1]) ? ' WHERE ' . $explode_where[1] : "";

            if($bounds) {
                $where = str_replace('Longitude', ' `t1`.Longitude', $where);
                $where = str_replace('Latitude', ' `t1`.Latitude', $where);
            }

            // Generate Query (To Locate Position of Listing)
            $query = "SELECT `x`.`" . $idx->field('ListingMLS') . "`, `x`.`position` FROM ("
            . "SELECT `t1`.`" . $idx->field('ListingMLS') . "`, @rownum := @rownum + 1 AS `position` "
            . "FROM ( "
            . "SELECT `t1`.`" . $idx->field('ListingMLS') . "`, `t1`.`" . $idx->field('ListingPrice') . "`, `t1`.`" . $idx->field('id') . "`, `t1`.`" . $idx->field('timestamp_created') . "` "
            . "FROM `" . $idx->getTable() . "` `t1` "
            // JOIN For Polygon Searches
            . (strpos($where, 'GeomFromText') !== false ? "JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType` " : "")
            . $where
            . ") `t1` "
            . "JOIN (SELECT @rownum := 0) `r` "
            . "ORDER BY " . $explode_order[1]
            . ") `x` WHERE `x`.`" . $idx->field('ListingMLS') . "` = '" . $dbIdx->cleanInput($mls_number) . "';";


            // Find Result Position
            $position = $dbIdx->fetchQuery($query);
            if (!empty($position)) {
                // If the listing is at the first position, set limit to 0, else - adjust 2 result spaces
                $pos = ($position['position'] == 1) ? 0 : $position['position'] - 2;

                // Fetch Results
                $results = array();
                $result = $dbIdx->query(
                    "SELECT " . $idx->selectColumns('`t1`.')
                    . " FROM `" . $idx->getTable() . "` `t1` "
                    . (strpos($where, 'GeomFromText') !== false ? "JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType` " : "")
                    . $where
                    . " ORDER BY " . $explode_order[1]
                    . " LIMIT " . $pos . ", 3;"
                );

                while ($record = $dbIdx->fetchArray($result)) {
                    $results[] = $record;
                }

                // if we are AT the first result we need to move the second result into the last spot and remove the first result.
                if ($position['position'] == 1) {
                    $results[2] = $results[1];
                    unset($results[0]);
                }

                // Previous Result
                if (!empty($results[0])) {
                    $result = $this->parseListing($results[0]);
                    if (!empty($result)) {
                        $paginate['prev'] = $result['url_details'];
                    }
                }

                // Next Result
                if (!empty($results[2])) {
                    $result = $this->parseListing($results[2]);
                    if (!empty($result)) {
                        $paginate['next'] = $result['url_details'];
                    }
                }
            }
        }

        $timer->stop();

        // Return Pagination
        return $paginate;
    }

    /**
     * Parse IDX Listing
     * @param array $listing
     * @return array
     */
    public function parseListing($listing)
    {
        if (!$this instanceof self) {
            $args = func_get_args();
            return Container::getInstance()->get(UtilIDXInterface::class)->parseListing($args[2]);
        }

        $idx = $idx = $this->idxFactory->getIdx();
        $dbIdx = $this->idxFactory->getDatabase();
        if (($manipulatedListing = $this->hooks->hook(HooksInterface::HOOK_IDX_PRE_PARSE_LISTING)->run($listing, $idx, $dbIdx))) {
            $listing = $manipulatedListing;
        }

        if (!empty($listing['ListingFeed'])) {
            $feed = $listing['ListingFeed'];
        } else {
            $feed = $idx->getName();
            $listing['ListingFeed'] = $feed;
        }

        \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($feed);

        // Parse Listing Function
        $function = str_replace('-', '_', $idx->getLink()) . '_parse_listing';
        if (function_exists($function)) {
            // Parse Listing
            $listing = $function ($idx, $dbIdx, $listing);
        }

        // Listing IDX
        $listing['idx'] = $idx->getLink();

        // Default Listing Image
        $listing['ListingImage'] = !empty($listing['ListingImage']) ? $listing['ListingImage'] : $this->settings['SETTINGS']['URL_IMG'] . 'no-image.jpg';

        // Unset Empty Latitude/Longitude Values (0.000000000000)
        if (isset($listing['Latitude']) && (empty($listing['Latitude']) || $listing['Latitude'] == 0)) {
            unset($listing['Latitude']);
        }
        if (isset($listing['Longitude']) && (empty($listing['Longitude']) || $listing['Longitude'] == 0)) {
            unset($listing['Longitude']);
        }

        // Unset invalid Virtual Tour URL
        $listing['VirtualTour'] = preg_match('/^https?:\/\//', $listing['VirtualTour']) ? $listing['VirtualTour'] : 'http://' . $listing['VirtualTour'];
        if (filter_var($listing['VirtualTour'], FILTER_VALIDATE_URL) === false) {
            unset($listing['VirtualTour']);
        }

        // Ignore CMS Listings
        if ($idx->getLink() !== 'cms') {
            // MLS # is Required, Return Empty Array
            if (empty($listing['ListingMLS'])) {
                return array();
            }

            // Format Listing Address and Set Default if Not Available
            $listing['Address'] = isset($listing['Address']) ? ucwords(strtolower($listing['Address'])) : '';
            $listing['Address'] = !empty($listing['Address']) ? $listing['Address'] : 'N/A';

            // Format ListingAgent
            $listing['ListingAgent']  = isset($listing['ListingAgent'])  ? ucwords(strtolower($listing['ListingAgent']))  : '';

            // URL Tags
            $tags = Lang::$lang['IDX_LISTING_TAGS'];
            if (!empty($tags) && is_array($tags)) {
                foreach ($tags as $k => $tag) {
                    if (!in_array($tag, array('ListingRemarks', 'ListingDOM'))) {
                        $tags[$tag] = $listing[$tag];
                    }
                    unset($tags[$k]);
                }
            }

            // Listing Link
            $listing['ListingLink'] = Format::slugify(Lang::write('IDX_LISTING_URL_PREFIX', $listing) . Lang::write('IDX_LISTING_URL', $tags));

            // Build Listing URLs
            $listing['url_details']             = $this->settings['SETTINGS']['URL'] . 'listing/' . $listing['ListingLink'] . '/';
            $listing['url_inquire']             = $listing['url_details'] . 'inquire/';
            $listing['url_phone']               = $listing['url_details'] . 'phone/';
            $listing['url_map']                 = $listing['url_details'] . 'map/';
            $listing['url_sendtofriend']        = $listing['url_details'] . 'friend/';
            $listing['url_birdseye']            = $listing['url_details'] . 'birdseye/';
            $listing['url_streetview']          = $listing['url_details'] . 'streetview/';
            $listing['url_brochure']            = $listing['url_details'] . 'brochure/';
            $listing['url_onboard']             = $listing['url_details'] . 'local/';
            $listing['url_register']            = $listing['url_details'] . 'register/';
            $listing['url_google-vr']           = $listing['url_details'] . 'google_vr/';

            // Multi-IDX
            if (!empty($this->settings['IDX_FEEDS'])) {
                if ($feed = $this->getFeed($idx->getLink())) {
                    if ((!empty($this->settings['IDX_FEED_DEFAULT']) && $this->settings['IDX_FEED_DEFAULT'] !== $feed) || (empty($this->settings['IDX_FEED_DEFAULT']) && $this->settings['IDX_FEED'] != $feed)) {
                        $url_keys = ['url_google-vr', 'url_details', 'url_phone', 'url_inquire', 'url_map', 'url_sendtofriend', 'url_birdseye', 'url_streetview', 'url_brochure', 'url_onboard', 'url_register'];
                        foreach ($url_keys as $key) {
                            if (isset($listing[$key])) {
                                $listing[$key] = str_replace(
                                    $this->settings['SETTINGS']['URL'] . 'listing/',
                                    $this->settings['SETTINGS']['URL'] . 'listing-' . $idx->getLink() . '/',
                                    $listing[$key]
                                );
                            }
                        }
                    }
                }
            }
        }

        global $_COMPLIANCE;

        if (!empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
            if (empty($this->settings->TRACKING_LOADED)) {
                $this->settings->TRACKING_LOADED = array();
            }

            $this->page->addJavascript('inc/js/idx/tracking/Tracking.js');

            foreach ($_COMPLIANCE['tracking'] as $service => $data) {
                // Extract data
                list ($init_data, $required_fields) = $data;
                unset($data);

                // Load tracking script

                if (empty($this->settings->TRACKING_LOADED[$service])) {
                    // This is here so it only gets loaded on pages where
                    // there is listing data.
                    $this->page->addJavascript('inc/js/idx/tracking/' . $service . '.js');

                    // Call the initialization function
                    $this->page->addSource(Source_Type::JAVASCRIPT, "tracking_" . $service . ".Init(" . json_encode($init_data) . ", " . json_encode($_GET['load_page']) . ");", 'dynamic', false);

                    $this->settings->TRACKING_LOADED[$service] = true;
                }
            }
        }

        // If Terms Are Required For This Listing.  This Check Is Needed For Comingled Feeds
        if (is_callable($_COMPLIANCE['terms_required'])) {
            if ($_COMPLIANCE['terms_required']($listing)) {
                $_COMPLIANCE['terms_required'] = true;
            }
        }

        // Is the listing image watermarked? This is typically on a per-feed basis, so a comingler
        // should return true/false based on the ListingFeed value, whereas a single feed should
        // just have a boolean value. Because this could be different for each listing in a
        // result set, lets store the result in IsWatermarked instead of overwriting the
        // Compliance setting.
        if (!isset($listing['IsWatermarked'])) {
            $listing['IsWatermarked'] = false;
            if (is_callable($_COMPLIANCE['is_watermarked'])) {
                if ($_COMPLIANCE['is_watermarked']($listing)) {
                    $listing['IsWatermarked'] = true;
                }
            } else if ($_COMPLIANCE['is_watermarked']) {
                $listing['IsWatermarked'] = true;
            }
        }

        // Compliance Auction Banner Display
        if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($listing)) {
            $listing['flag'] = 'Auction';
        }

        if (($manipulatedListing = $this->hooks->hook(HooksInterface::HOOK_IDX_POST_PARSE_LISTING)->run($listing, $idx, $dbIdx))) {
            $listing = $manipulatedListing;
        }

        // Return Listing
        return $listing;
    }

    /**
     * Get History for an IDX Listing (Price Changes & Status Changes)
     * <code>
     *  $history = \Container::getInstance()->get(\REW\Core\Interfaces\Util\IDXInterface::class)->getHistory($listing);
     * </code>
     * @param array $listing
     * @return array
     * @uses $this->settings->MODULES['REW_IDX_HISTORY_PRICE']
     * @uses $this->settings->MODULES['REW_IDX_HISTORY_STATUS']
     */
    public function getHistory($listing)
    {
        $dbIdx = $this->idxFactory->getDatabase();

        // History
        $history = array();
        $order = array();

        // Price Change History
        if (!empty($this->settings->MODULES['REW_IDX_HISTORY_PRICE'])) {
            $query = "SELECT 'Price' AS `Type`, `ListingPriceOld` AS `Old`, `ListingPriceNew` AS `New`, UNIX_TIMESTAMP(`timestamp_created`) AS `Date`"
                . " FROM `_track_price_history`"
                . " WHERE `ListingMLS` = '" . $dbIdx->cleanInput($listing['ListingMLS']) . "' AND `ListingType` = '" . $dbIdx->cleanInput($listing['ListingType']) . "'"
                . " ORDER BY `timestamp_created` DESC LIMIT 50;";
            if ($result = $dbIdx->query($query)) {
                while ($change = $dbIdx->fetchArray($result)) {
                    // Price Difference
                    $change['Diff'] = ($change['New'] - $change['Old']);
                    // Change Details
                    $change['Details'] = ($change['Diff'] < 0 ? 'Price Reduced' : 'Price Increased');
                    $change['Details'] .= ' from $' . Format::number($change['Old']);
                    $change['Details'] .= ' to $' . Format::number($change['New']);
                    // Add to History
                    $history[] = $change;
                    $order[] = $change['Date'];
                }
            } else {
                Log::error('Query Error: ' . $dbIdx->error());
            }
        }

        // Status Change History
        if (!empty($this->settings->MODULES['REW_IDX_HISTORY_STATUS'])) {
            $query = "SELECT 'Status' AS `Type`, `ListingStatusOld` AS `Old`, `ListingStatusNew` AS `New`, UNIX_TIMESTAMP(`timestamp_created`) AS `Date`"
                . " FROM `_track_status_history`"
                . " WHERE `ListingMLS` = '" . $dbIdx->cleanInput($listing['ListingMLS']) . "' AND `ListingType` = '" . $dbIdx->cleanInput($listing['ListingType']) . "'"
                . " ORDER BY `timestamp_created` DESC LIMIT 50;";
            if ($result = $dbIdx->query($query)) {
                while ($change = $dbIdx->fetchArray($result)) {
                    // Change Details
                    $change['Details'] = 'Status Changed from ' . $change['Old'] . ' to ' . $change['New'];
                    // Add to History
                    $history[] = $change;
                    $order[] = $change['Date'];
                }
            } else {
                Log::error('Query Error: ' . $dbIdx->error());
            }
        }

        // Sort History by Date
        array_multisort($order, SORT_DESC, $history);

        // Return History
        return $history;
    }

    /**
     * Parse Criteria to Generate Readable String
     * @param array $criteria
     * @return string Readable String (contains HTML)
     */
    public function parseCriteria($criteria)
    {

        if (!$this instanceof self) {
            $args = func_get_args();
            return Container::getInstance()->get(UtilIDXInterface::class)->parseCriteria($args[0]);
        }

        // Available Search Fields
        $idx = $this->idxFactory->getIdx();
        $fields = search_fields($idx);
        $fields['search_location'] = array('name' => 'Search Location');
        if (!empty($this->settings->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', $this->settings->ADDONS)) {
            $fields = array_merge($fields, DriveTime::IDX_SAVED_SEARCH_FIELDS);
        }

        // Output
        $output = '';

        // Parse Criteria
        if (!empty($criteria) && is_array($criteria)) {
            // Sort by Key
            ksort($criteria);

            // Process Criteria
            foreach ($criteria as $field => $values) {
                // Skip Empty
                $values = is_array($values) ? array_filter($values) : $values;
                if (empty($values)) {
                    continue;
                }

                // Map Data
                if ($field == 'map' && is_array($values)) {
                    foreach ($values as $field => $value) {
                        // Skip Empty
                        if (empty($value)) {
                            continue;
                        }

                        // Only Radius & Polygon
                        if (in_array($field, array('radius', 'polygon', 'bounds'))) {
                            // Bounds Search
                            if ($field == 'bounds') {
                                $field = 'Search in Bounds';
                                $value = 'See Map';
                            }

                            // Polygon Search
                            if ($field == 'polygon') {
                                $field = 'Polygon';
                                $value = 'See Map';
                            }

                            // Radius Search
                            if ($field == 'radius') {
                                $field = 'Radius';
                                if (is_array($value)) {
                                    $value = 'See Map';
                                } else {
                                    $value = json_decode($value, true);
                                    list($latitude, $longitude, $radius) = explode(',', $value[0]);
                                    $value = round($radius, 3) . ' miles from point.';
                                    // @todo: miles vs kilometers
                                }
                            }

                            // Append to Output
                            $output .= '<strong>' . $field. ':</strong> ' . (is_array($value) ? implode(', ', $value) : $value) . '<br>' . PHP_EOL;
                        }
                    }
                } else if ($field == 'drivetime' && is_array($values['filters'])) {
                    $output.= sprintf('<strong>Drivetime:</strong> %s Minutes %s "%s" at %s<br>',
                        $values['filters']['duration'],
                        (($values['filters']['direction'] === 'A') ? 'to' : 'from'),
                        $values['filters']['address'],
                        date('ga', strtotime($values['filters']['arrivalTime']))
                    ) . PHP_EOL;;
                } else {
                    // Format Prices
                    if (in_array($field, array('minimum_price', 'maximum_price', 'minimum_rent', 'maximum_rent'))) {
                        $values = '$' . Format::number($values);
                    }

                    // Format Numbers
                    if (in_array($field, array('minimum_sqft', 'maximum_sqft'))) {
                        $values = Format::number($values);
                    }

                    // Format Drive Time Values
                    if (in_array($field, DriveTime::IDX_FORM_FIELDS)) {
                        switch ($field) {
                            case 'dt_direction' :
                                $values = ($value === 'A') ? 'To' : 'From';
                                break;
                            case 'dt_travel_duration' :
                                $values .= ' mins';
                                break;
                            case 'dt_arrival_time' :
                                $values = date('ga', strtotime($values));
                                break;
                        }
                    }

                    // Append to Output
                    if (!empty($fields[$field]['name'])) {
                        $output .= '<strong>' . $fields[$field]['name'] . ':</strong> ' . Format::htmlspecialchars(is_array($values) ? implode(', ', $values) : $values) . '<br>' . PHP_EOL;
                    }
                }
            }
        }

        // Return Output
        return $output;
    }

    /**
     * Get IDX Object by Feed Name (Folder Name)
     *
     * @param string $feed
     * @return \REW\Core\Interfaces\IDXInterface
     * @throws Exception
     * @deprecated Use IDXFactoryInterface::getIdx() instead.
     */
    public static function getIdx($feed = null)
    {
        return Container::getInstance()->get(IDXFactoryInterface::class)->getIdx($feed);
    }

    /**
     * Get IDX Object by Feed Name (Folder Name)
     *
     * @param string $feed
     * @return \REW\Core\Interfaces\DatabaseInterface
     * @throws Exception
     * @deprecated Use IDXFactoryInterface::getDatabase() instead.
     */
    public static function getDatabase($feed = null)
    {
        return Container::getInstance()->get(IDXFactoryInterface::class)->getDatabase($feed);
    }

    /**
     * The method accepts a list of feeds and splits any commingled feeds it finds
     * in to their individual components.  Returns a list of individual feeds that were passed
     * broken up commingled feeds (the commingled feed name itself is removed)
     * @param array $feeds array of feeds
     * @return string array of feeds
     * @deprecated Use IDXFactoryInterface::parseFeeds() instead.
     */
    public static function parseFeeds($feeds = array())
    {
        return Container::getInstance()->get(IDXFactoryInterface::class)->parseFeeds($feeds);
    }

    /**
     * Find feed name by settings folder, if feed is supported
     * @param string $slug
     * @return string|NULL
     * @deprecated Use IDXFactoryInterface::getFeed() instead.
     */
    public static function getFeed($slug)
    {
        return Container::getInstance()->get(IDXFactoryInterface::class)->getFeed($slug);
    }

    /**
     * Load IDX Settings
     *
     * @return void
     * @deprecated Use IDXFactoryInterface::loadSettings() instead.
     */
    public static function loadSettings()
    {
        Container::getInstance()->get(IDXFactoryInterface::class)->loadSettings();
    }

    /**
     * Switch to the specified feed & update URLs
     * @param string $slug
     * @deprecated Use IDXFactoryInterface::switchFeed() instead.
     */
    public static function switchFeed($slug)
    {
        return Container::getInstance()->get(IDXFactoryInterface::class)->switchFeed($slug);
    }
}
