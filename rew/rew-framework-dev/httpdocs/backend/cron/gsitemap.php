<?php

// Running via HTTP
if (isset($_SERVER['HTTP_HOST'])) {
    // Not Authorized
    die('Not Authorized');
    //echo '<pre>';

// Running via CLI
} else {
    // Set DOCUMENT_ROOT & HTTP_HOST
    $_SERVER['DOCUMENT_ROOT'] = $argv[1];
    $_SERVER['HTTP_HOST'] = $argv[2];
    $_SERVER['REQUEST_SCHEME'] = $argv[3];
}

// Start Time
$start = time();

// Include Backend Config
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

// Build website URL from superglobals
$httpSchema = $_SERVER['REQUEST_SCHEME'];
$httpHost = $_SERVER['HTTP_HOST'];
$url = sprintf('%s://%s/', $httpSchema, $httpHost);

// Create Sitemap Index
$sm = new Sitemap_Indexed();

// Add Home page
$sm->add($url, 1.0);

// Select CMS Pages
$query = "SELECT * FROM `" . TABLE_PAGES . "` WHERE `agent` = '" . Settings::getInstance()->SETTINGS['agent'] . "' AND `is_link` != 't' AND `hide_sitemap` != 't' AND `file_name` NOT IN ('" . implode("', '", unserialize(REQUIRED_PAGES)) . "') ORDER BY (`category_order` * 100) + `subcategory_order` ASC;";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_array($result)) {
        // Add URL to Sitemap
        $sm->add($url . urlencode($row['file_name']) . '.php');
    }
}

// Add Blog Entries to XML Sitemap
if (!empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    // Include Blog Config
    include_once $_SERVER['DOCUMENT_ROOT'] . '/blog/common.inc.php';

    // Add Main Blog page
    $sm->add($url . 'blog/', 1.0);

    // Select Blog Entries
    $query = "SELECT `link` FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true' AND `timestamp_published` < NOW() ORDER BY `timestamp_published` DESC;";
    if ($result = mysql_query($query)) {
        while ($row = mysql_fetch_array($result)) {
            // Add URL to Sitemap
            $sm->add(sprintf(URL_BLOG_ENTRY, $row['link']));
        }
    }
}

// Add Listings to XML Sitemap

// IDX Feeds
$feeds = array();
if (!empty(Settings::getInstance()->MODULES['REW_IDX_SITEMAP'])) {
    $feeds[] = Settings::getInstance()->IDX_FEED;

    // Add Main IDX pages
    $sm->add($url . 'idx/', 1.0);
    $sm->add($url . 'idx/map/', 1.0);
}

// Multi-IDX
if (!empty(Settings::getInstance()->IDX_FEEDS)) {
    foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) {
        if (in_array($feed, $feeds)) {
            continue;
        }

        // Add Multi-IDX pages
        $sm->add($url . 'idx/' . $feed . '/', 1.0);
        $sm->add($url . 'idx/map/' . $feed . '/', 1.0);

        $feeds[] = $feed;
    }
}

// Needs to be added last to work properly
$feeds[] = 'cms';

// Process Feeds
foreach ($feeds as $feed) {
    // Switch feed
    Util_IDX::switchFeed($feed);

    // IDX Objects
    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();

    // Refined $_CLIENT['city_list']
    if (!empty($_CLIENT['city_list']) && $feed != 'cms') {
        if (empty($_REQUEST['search_city'])) {
            $_REQUEST['search_city'] = array();
            foreach ($_CLIENT['city_list'] as $city) {
                $_REQUEST['search_city'][] = $city['value'];
            }
        }
    }

    // IDX Search Fields
    $searchFields = search_fields($idx);

    //  Build Search Query
    $search_where = '';
    if (is_array($searchFields)) {
        foreach ($searchFields as $searchField) {
            $searchValue = $_REQUEST[$searchField['form_field']];
            $search_db_field = $idx->field($searchField['idx_field']);
            if (!empty($searchValue)) {
                $search_where .= $db_idx->buildQueryString($search_db_field, $searchValue, $searchField['match'], 'AND');
            }
        }
    }
    $search_where = rtrim($search_where, 'AND ');

    // Any global criteria
    $idx->executeSearchWhereCallback($search_where);

    // Build SELECT Query
    $query = "SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "`"
           . (!empty($search_where) ? " WHERE " . $search_where : '') . ";";

    // Select Listings
    if ($listings = $db_idx->query($query, MYSQLI_USE_RESULT)) {
        while ($listing = $db_idx->fetchArray($listings)) {
            // Listing Details
            $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

            // Add URL to Sitemap
            $sm->add($listing['url_details']);
        }
        $listings->close();
    }

    // Clear $_REQUEST for next feed
    unset($_REQUEST['search_city']);
}

// BDX Sitemap
if (!empty(Settings::getInstance()->MODULES['REW_BUILDER'])) {
    // Build app object
    require_once $_SERVER['DOCUMENT_ROOT'] . '/builders/appBuilder.php';

    // Set snippet flag to ensure URLs are built correctly
    $app->snippet = true;

    // Build Search Environment
    $env = $app->environment();
    $env['SCRIPT_NAME'] = '';

    // Add Main BDX page
    $sm->add($url . 'builders/', 1.0);

    // Build state list from settings
    $states = array();
    if (!empty($app->bdx_settings['states']) && is_array($app->bdx_settings['states'])) {
        if (is_array(BDX\Settings::getInstance()->STATES)) {
            foreach ($app->bdx_settings['states'] as $key => $val) {
                if (in_array($key, BDX\Settings::getInstance()->STATES)) {
                    $states[$key] = isset($val['cities']) ? $val['cities'] : null;
                }
            }
        } else {
            foreach ($app->bdx_settings['states'] as $key => $val) {
                $states[$key] = isset($val['cities']) ? $val['cities'] : null;
            }
        }
    } else {
        if (is_array(BDX\Settings::getInstance()->STATES)) {
            foreach (BDX\Settings::getInstance()->STATES as $state) {
                $states[$state] = null;
            }
        }
    }

    // Build Where if states settings are populated
    $sql_where = array();
    if (!empty($states)) {
        foreach ($states as $state => $cities) {
            if (!empty($cities)) {
                array_walk($cities, function (&$val, &$key) use ($app) {
                    $val = $app->db_bdx->quote($val);
                });
            }
            $sql_where[] = "( `Subdivision`.`State` = '".$state."' ".
                (!empty($cities) ? PHP_EOL." AND `Subdivision`.`City` IN (".PHP_EOL.implode(",".PHP_EOL, $cities).") ".PHP_EOL : "")
            .")";
        }
    }
    if (!empty($sql_where)) {
        $sql_where = " WHERE ".PHP_EOL.implode(' OR '.PHP_EOL, $sql_where);
    }

    try {
        // Select BDX Communities
        $searchQuery = $app->db_bdx->prepare("SELECT `Subdivision`.`State`, `Subdivision`.`City`, `Subdivision`.`SubdivisionID`, `Subdivision`.`SubdivisionName`"
                . " FROM `" . BDX\Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                . " LEFT JOIN `" . BDX\Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                . $sql_where
                . " GROUP BY `Subdivision`.`SubdivisionID`"
                . ";");

        // Execute Search
        $searchQuery->execute();

        while ($result = $searchQuery->fetch()) {
            // Parse Community
            $result = BDX\Community::parse($result, $app);

            // Add URL to Sitemap
            $sm->add($result['Link']);
        }

        // Select BDX Listings
        $searchQuery = $app->db_bdx->prepare("SELECT `Subdivision`.`State`, `Subdivision`.`City`, `Subdivision`.`SubdivisionID`, `Subdivision`.`SubdivisionName`, `Listing`.`PlanName`, `Listing`.`ListingID`"
                . " FROM `" . BDX\Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                . " LEFT JOIN `" . BDX\Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                . $sql_where
                . " GROUP BY `Listing`.`ListingID`"
                . ";");

        // Execute Search
        $searchQuery->execute();

        while ($result = $searchQuery->fetch()) {
            // Parse Listing
            $result = BDX\Listing::parse($result, $app);

            // Add URL to Sitemap
            $sm->add($result['Link']);
        }

    // Error Occurred
    } catch (Exception $e) {
        Log::error($e);
    }
}

// Save Sitemap
$sm->save();

// Submit to Google
$url = 'http://www.google.com/webmasters/tools/ping?sitemap=' . urlencode($url . 'sitemap.xml');

// cURL Resource
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Output
echo 'Response Code: ' . var_export($code, true) . PHP_EOL;
echo 'Response Data: ' . PHP_EOL . PHP_EOL . $data;

// Calculate Execution Time
$runTime    = time() - $start;
$hours      = floor($runTime / 3600);
$runTime    -= ($hours * 3600);
$minutes    = floor($runTime / 60);
$runTime    -= ($minutes * 60);
$seconds    = $runTime;

// Output
echo PHP_EOL . PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
