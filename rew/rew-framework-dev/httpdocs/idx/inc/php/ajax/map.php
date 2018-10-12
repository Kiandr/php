<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\LogInterface;

// Map Search
$_GET['load_page'] = 'search_map';

// Include IDX Configuration
include_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

$container = Container::getInstance();
$log = $container->get(LogInterface::class);
$settings = $container->get(SettingsInterface::class);

// Mapping Not Enabled
if (empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    die('{}');
}

// JSON Data
$json = array();

// Use Clustering
$json['cluster'] = true;

// AJAX Process ID
if (!empty($_POST['pid'])) {
    $json['pid'] = $_POST['pid'];
}

/**
 * Search Map Layers
 */
if (isset($_GET['searchLayers'])) {
    // Close session
    @session_write_close();

    // Onboard Database
    define('ONBOARD_DATABASE', 'onboard');

    // Onboard Tables
    define('ONBOARD_TABLE_SCHOOLS', 'onboard_schools_' . Settings::getInstance()->SETTINGS['map_state']);
    define('ONBOARD_TABLE_AMENITIES', 'onboard_amenities_' . Settings::getInstance()->SETTINGS['map_state']);

    $db_onboard = DB::get('onboard');

    // Search Limit
    $limit = 100;

    // Query Collection
    $search_where = !empty($search_where) ? array($search_where) : array();

    // Search Group
    $search_group = array();

    // Search in Polygons
    $polygons = $idx->buildWherePolygons($_POST['polygon'], $search_group, $search_having, "GeomFromText(CONCAT('POINT(', `LATITUDE`, ' ', `LONGITUDE`, ')'))");

    // Search in Radiuses
    $radiuses = $idx->buildWhereRadius($_POST['radius'], $search_group, 'LATITUDE', 'LONGITUDE');

    // Search in Bounds
    if (empty($polygons) && empty($radiuses)) {
        $bounds = $idx->buildWhereBounds($_POST['ne'], $_POST['sw'], $search_where, '`LATITUDE`', '`LONGITUDE`');
    }

    // Add to Search Criteria
    if (!empty($search_group)) {
        $search_where[] = "(" . implode(" OR ", $search_group) . ")";
    }

    // Replace User-Defined GISWithin SQL Function with ST_WITHIN
    //if (!empty($search_having[0])) $search_having[0] = preg_replace('/GISWithin\((.*), (.*)\)/', 'ST_WITHIN($1, GeomFromText(CONCAT(\'MULTIPOLYGON(((\',$2,\')))\')))', $search_having[0]);

    // Query String
    $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '');// . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

    // Load Map Layer
    if (!empty($_POST['type'])) {
        // Select Query
        switch ($_POST['type']) {
            case 'schools':
                $query = "SELECT SQL_NO_CACHE `INSTITUTION_NAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `LOCATION_ADDRESS` AS `address`, `LOCATION_CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_SCHOOLS . "` " . (!empty($search_where) ? " WHERE " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
            case 'hospitals':
                $query = "SELECT SQL_NO_CACHE `BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_AMENITIES . "` WHERE `CATEGORY` = 'HEALTH CARE SERVICES' AND `LINEOFBUS` = 'HOSPITALS'" . (!empty($search_where) ? " AND " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
            case 'airports':
                $query = "SELECT SQL_NO_CACHE `BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_AMENITIES . "` WHERE `CATEGORY` = 'TRAVEL' AND `LINEOFBUS` = 'AIRPORTS'" . (!empty($search_where) ? " AND " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
            case 'parks':
                $query = "SELECT SQL_NO_CACHE `BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_AMENITIES . "` WHERE `CATEGORY` = 'ATTRACTIONS - RECREATION ' AND `LINEOFBUS` = 'OUTDOOR ACTIVITES' AND `INDUSTRY` = 'PARKS'" . (!empty($search_where) ? " AND " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
            case 'golf-courses':
                $query = "SELECT SQL_NO_CACHE `BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_AMENITIES . "` WHERE `CATEGORY` = 'ATTRACTIONS - RECREATION' AND `LINEOFBUS` = 'GOLF'" . (!empty($search_where) ? " AND " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
            case 'churches':
                $query = "SELECT SQL_NO_CACHE `BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_AMENITIES . "` WHERE `CATEGORY` = 'ORGANIZATIONS - ASSOCIATIONS' AND `LINEOFBUS` = 'PLACE OF WORSHIP' AND `INDUSTRY` = 'CHURCHES'" . (!empty($search_where) ? " AND " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
            case 'shopping':
                $query = "SELECT SQL_NO_CACHE `BUSNAME` AS `title`, `LATITUDE` AS `latitude`, `LONGITUDE` AS `longitude`, `STREET` AS `address`, `CITY` AS `city`, `ZIP` AS `zip` FROM `" . ONBOARD_DATABASE . "`.`" . ONBOARD_TABLE_AMENITIES . "` WHERE `CATEGORY` = 'SHOPPING' AND `LINEOFBUS` = 'SHOPPING CENTERS AND MALLS'" . (!empty($search_where) ? " AND " . $search_where : '') . (!empty($search_limit) ? $search_limit : '');
                break;
        }

        $result = $db_onboard->prepare($query);

        // Execute Query
        try {
            $query_success = $result->execute();
        } catch (PDOException $e) {
            $json['error'] = 'Failed to get Onboard Amenities'; // Use a human-readable error
            //$json['error'] = $e->getMessage();  // MySQL PDO error
        }

        // If Execute Success Fetch Data
        if ($query_success) {
            $result = $result->fetchAll(PDO::FETCH_ASSOC);

            // Return Code
            $json['returnCode'] = 200;

            // Results
            $json['results'] = array();

            // Return Results
            foreach ($result as $row) {
                // Only Return Points in Polygons
                if (is_array($polygons) && !empty($polygons)) {
                    $locate = true;
                    foreach ($polygons as $polygon) {
                        $locate = Map::findPoint($polygon, $row['latitude'], $row['longitude']);
                        if (!empty($locate)) {
                            break;
                        }
                    }
                    if (empty($locate)) {
                        continue;
                    }
                }

                // Tooltip HTML
                $tooltip = '<div class="popover">'
                    . '<header class="title">'
                        . '<strong>' . ucwords(strtolower($row['title'])) . '</strong>'
                        . '<a href="javascript:void(0);" class="action-close hidden">&times;</a>'
                    . '</header>'
                    . '<div class="body">'
                        . '<p>' . ucwords(strtolower($row['address'])) . ', ' . ucwords(strtolower($row['city'])) . ' ' . $row['zip'] . '</p>'
                    . '</div>'
                    . '<div class="tail"></div>'
                . '</div>';

                // Add to Collection
                $json['results'][] = array(
                    'tooltip'   => $tooltip,
                    'lat'       => $row['latitude'],
                    'lng'       => $row['longitude']
                );

                // Return Maximum Results
                if (count($json['results']) >= $limit) {
                    break;
                }
            }

            // Randomize Results
            shuffle($json['results']);
        }
    }
}

/**
 * Search IDX Listings
 */
if (isset($_GET['searchListings'])) {
    // Remember Search URL
    if (!empty($_POST['search_url'])) {
        $user->setBackUrl("/idx/map/" . $_POST['search_url']);
        $user->setSearchUrl($_POST['search_url']);
        $user->setRedirectUrl($_POST['search_url']);
    }

    // Close session
    @session_write_close();

    // Search Criteria
    $criteria = array();

    // POST Search Criteria
    if (!empty($_POST['criteria'])) {
        parse_str($_POST['criteria'], $criteria);
    }

    // Process Search Criteria
    if (!empty($criteria)) {
        // Select Requested IDX
        $idx = Util_IDX::getIdx($criteria['idx']);

        // Search Query
        $search_where = '';

        // Build and Set DriveTime Polygon
        if (!empty($settings->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
            $drive_time = $container->get(DriveTime::class);
            try {
                $travel_poly = $drive_time->authenticateAndBuildPolygon(
                    $criteria['dt_address'],
                    $criteria['place_lat'],
                    $criteria['place_lng'],
                    $criteria['dt_direction'],
                    $criteria['dt_travel_duration'],
                    $criteria['dt_arrival_time']
                );
            } catch (Exception $e) {
                $log->error($e->getMessage());
            }
            if (!empty($travel_poly)) {
                $_POST['polygon'] = $travel_poly;
            }
        }

        // Set as $_REQUEST
        $request  = $_REQUEST;
        $_REQUEST = $criteria;

        // Build Query
        $search_vars        = $idx->buildWhere($idx, $db_idx, 't1');
        $search_where       = $search_vars['search_where'];
        $search_title       = $search_vars['search_title'];
        $search_criteria    = $search_vars['search_criteria'];

        // Reset $_REQUEST
        $criteria = $_REQUEST;
        $_REQUEST = $request;
    } else {
        $search_where = '';

        // Any global criteria
        $idx->executeSearchWhereCallback($search_where);
    }

    // Search Title
    if (!empty($search_title)) {
        if (!empty($_POST['search_title'])) {
            $json['search_title'] = strip_tags($_POST['search_title']);
        } else {
            $json['search_title'] = strip_tags($search_title);
        }
    }

    // Query Collection
    $search_where = !empty($search_where) ? array($search_where) : array();

    // Search Groups
    $search_group = array();

    // Latitude / Longitude Columns
    $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
    $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

    // Search in Bounds
    if (!empty($_POST['bounds'])) {
        $bounds = $idx->buildWhereBounds($_POST['ne'], $_POST['sw'], $search_where, $col_latitude, $col_longitude);
    }

    // Search in Radiuses
    $radiuses = $idx->buildWhereRadius($_POST['radius'], $search_group, $col_latitude, $col_longitude);

    // Search in Polygons
    $polygons = $idx->buildWherePolygons($_POST['polygon'], $search_group, $search_having, 't2.Point');
    if (!empty($polygons)) {
        $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
    }

    // Add to Search Criteria
    if (!empty($search_group)) {
        $search_where[] = "(" . implode(" OR ", $search_group) . ")";
    }

    // Query String
    $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

    // Search Order
    if (!empty($_POST['sortorder'])) {
        list($_REQUEST['sort'], $_REQUEST['order']) = explode('-', $_POST['sortorder']);
    }
    $sql_order = !empty($_REQUEST['order']) ? $_REQUEST['order'] : $idx->getSearchOrder();
    $sql_sort  = !empty($_REQUEST['sort'])  ? $_REQUEST['sort']  : $idx->getSearchSort();
    if (!empty($sql_order) && !empty($sql_sort)) {
        $search_order = " ORDER BY `" . $idx->field($sql_order) . '` ' . $sql_sort;
    } else {
        $search_order = '';
    }

    // Generate Count Query
    $count_query = "SELECT SQL_CACHE COUNT(*) AS total"
                . " FROM (SELECT `t1`.`" . $idx->field('ListingMLS') . "` AS `total`"
                    . (!empty($polygons) ? ", `t2`.`Point`" : "")
                    . " FROM `" . $idx->getTable() . "` `t1` "
                    . (!empty($polygons) ? " JOIN `" . $idx->getTable('geo') . "` `t2`"
                        . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
                        . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
                    : "")
                    . (!empty($search_where) ? ' WHERE ' . $search_where : '')
                . ") AS `listings`;";

    // Result Count
    $count = $db_idx->fetchQuery($count_query);
    $json['count'] = (int) $count['total'];
    $json['total'] = (int) $count['total'];
    $json['polygon_data'] = $_POST['polygon'];

    // Search Limit
    $searchlimit = isset($_COMPLIANCE['limit']) ? $_COMPLIANCE['limit'] : 250;

    // Search Query Limit
    $limitvalue = $json['count'] < 1000 ? $json['count'] : 1000;
    $search_limit = " LIMIT 0," . $limitvalue;

    // Generate Query
    $query = "SELECT SQL_CACHE " . $idx->selectColumns('`t1`.')
        . (!empty($polygons) ? ", `t2`.`Point`" : "")
        . " FROM `" . $idx->getTable() . "` `t1` "
        . (!empty($polygons) ? " JOIN `" . $idx->getTable('geo') . "` `t2`"
            . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
            . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
        : "")
        . " WHERE 1"
        . (!empty($polygons) ? " AND `t2`.`ListingMLS` IS NOT NULL" : "")
        . (!empty($search_where) ? ' AND ' . $search_where : '')
        . $search_order
        . $search_limit
    . ";";

    // Execute Query
    if ($result = $db_idx->query($query)) {
        // Check Count vs Limit
        if ($json['count'] >= $searchlimit) {
            $json['limit'] = (int) $searchlimit;
        }

        // Return Code
        $json['returnCode'] = 200;

        // Results
        $json['results'] = array();

        // Return Results
        while ($listing_result = $db_idx->fetchArray($result)) {
            // Parse Listing
            $listing_result = Util_IDX::parseListing($idx, $db_idx, $listing_result);

            // Require Co-Ordinates
            if (empty($listing_result['Latitude']) || empty($listing_result['Longitude'])) {
                continue;
            }

            // Tooltip
            ob_start();
            $listing_tooltip = $listing_result;
            include Page::locateTemplate('idx', 'misc', 'tooltip');
            $tooltip = str_replace(array("\r\n", "\n", "\t"), '', trim(ob_get_clean()));

            // Add to Collection
            $json['results'][] = array(
                'tooltip'   => $tooltip,
                'lat'       => $listing_result['Latitude'],
                'lng'       => $listing_result['Longitude']
            );

            // Maximum Results Reached
            if (count($json['results']) >= $searchlimit) {
                break;
            }
        }

        // Free Result
        $result->close();

        // Search Statistics
        $json['stats'] = array();

        // Generate Search Statistics Query
        $stats_query = "SELECT SQL_NO_CACHE "
            . " COUNT(*) AS `total`"
            . " , AVG(`price`) AS `price_avg`"
            . " , MAX(`price`) AS `price_high`"
            . " , MIN(NULLIF(`price`, 0)) AS `price_low`"
            . " FROM (SELECT "
                . " `t1`.`" . $idx->field('ListingMLS') . "` AS `mls`"
                . " , `t1`.`" . $idx->field('ListingPrice') . "` AS `price`"
                . (!empty($polygons) ? " , `t2`.`Point`" : "")
                . " FROM `" . $idx->getTable() . "` `t1`"
                . (!empty($polygons) ? " JOIN `" . $idx->getTable('geo') . "` `t2`"
                    . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
                    . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
                : "")
                . (!empty($search_where) ? ' WHERE ' . $search_where : '')
            . ") AS `stats`"
        . ";";

        // Search Statistics
        $stats = $db_idx->fetchQuery($stats_query);
        if (!empty($stats)) {
            $json['stats'] = array_merge($json['stats'], array(
                'total'         => number_format($stats['total']),
                'price_avg'     => '$' . number_format($stats['price_avg']),
                'price_high'    => '$' . number_format($stats['price_high']),
                'price_low'     => '$' . number_format($stats['price_low'])
            ));
        }

        // Generate Search Statistics Query
        $stats_query = "SELECT SQL_NO_CACHE "
            . " AVG(`t1`.`" . $idx->field('NumberOfSqFt') . "`) AS `sqft_avg`, "
            . " MAX(`t1`.`" . $idx->field('NumberOfSqFt') . "`) AS `sqft_high`, "
            . " MIN(NULLIF(`t1`.`" . $idx->field('NumberOfSqFt') . "`, 0)) AS `sqft_low`"
            . (!empty($polygons) ? ", `t2`.`Point`" : "")
            . " FROM `" . $idx->getTable() . "` `t1`"
            . (!empty($polygons) ? " LEFT JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
                . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
            : "")
            . " WHERE `t1`.`" . $idx->field('NumberOfSqFt') . "` > 0"
            . (!empty($search_where) ? ' AND ' . $search_where : '');

         // Search Statistics
         $stats = $db_idx->fetchQuery($stats_query);
        if (!empty($stats)) {
            $json['stats'] = array_merge($json['stats'], array(
               'sqft_avg'  => number_format($stats['sqft_avg']) . ' ft&sup2;',
               'sqft_high' => number_format($stats['sqft_high']) . ' ft&sup2;',
               'sqft_low'  => number_format($stats['sqft_low']) . ' ft&sup2;'
            ));
        }

    // Query Error
    } else {
        $json['error'] = $db_idx->error();
    }
}

// Send as JSON
header('Content-Type: application/json');

// Return JSON
die(json_encode($json));
