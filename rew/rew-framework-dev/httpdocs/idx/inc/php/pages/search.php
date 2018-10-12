<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\SettingsInterface;

$container = Container::getInstance();
$settings = $container->get(SettingsInterface::class);

// Remember this Page
$user->setBackUrl($_SERVER['REQUEST_URI']);
$user->setSearchUrl($_SERVER['REQUEST_URI']);
$user->setRedirectUrl($_SERVER['REQUEST_URI']);

// Agent, Create / Edit Saved Search
if ((!empty($_REQUEST['create_search']) || !empty($_REQUEST['edit_search'])) && !empty($_REQUEST['lead_id'])) {
    global $lead;
    $lead = $db_users->fetchQuery("SELECT `id`, `first_name`, `last_name` FROM `" . TABLE_USERS . "` WHERE `id` = '" . $db_users->cleanInput($_REQUEST['lead_id']) . "';");
}

// Reset Page Title
$page_title = '';

// Load Saved Search
if (!empty($_REQUEST['saved_search_id'])) {
    // Select IDX Search
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $db_users->cleanInput($_REQUEST['saved_search_id']) . "';");

    // Saved Search
    global $saved_search;
    $saved_search = $search;

// Load Custom Search
} else if (!empty($_REQUEST['search_id'])) {
    // Select IDX Search
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_SEARCHES . "` WHERE MD5(`id`) = '" . $db_users->cleanInput($_REQUEST['search_id']) . "';");

    // Page Title
    $page_title = $search['title'];

// Load Defaults (If not IDX Snippet or Saved Search)
} else if (empty($_REQUEST['snippet']) && empty($_REQUEST['save_search'])) {
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '" . Settings::getInstance()->IDX_FEED . "';");
    if (empty($search)) {
        $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '';");
    }
}

// Load Search
if (!empty($search)) {
    // Order / Sort
    if (!empty($search['sort_by'])) {
        list ($search['sort'], $search['order']) = explode('-', $search['sort_by'], 2);
    }
    $_REQUEST['order'] = isset($_REQUEST['order']) ? $_REQUEST['order'] : $search['order'];
    $_REQUEST['sort']  = isset($_REQUEST['sort'])  ? $_REQUEST['sort']  : $search['sort'];

    // Page Limit
    if (!empty($search['page_limit'])) {
        $_REQUEST['page_limit'] = isset($_REQUEST['page_limit']) ? $_REQUEST['page_limit'] : $search['page_limit'];
    }

    // Search Criteria
    if (!empty($search['criteria'])) {
        // Un-Serialize Criteria
        $criteria = unserialize($search['criteria']);
        if (!empty($criteria) && is_array($criteria)) {
            // Only Set Criteria If Not Refined
            if (empty($_REQUEST['refine']) && empty($_REQUEST['search_by'])) {
                // Searchable IDX Fields
                $search_fields = search_fields($idx);
                $search_fields = array_keys($search_fields);
                $search_fields = array_merge(DriveTime::IDX_FORM_FIELDS, array('map', 'view', 'search_location', 'order', 'sort'), $search_fields);

                // Set $_REQUEST
                foreach ($search_fields as $field) {
                    if (isset($criteria[$field])) {
                        if (!isset($_REQUEST[$field])) {
                            $_REQUEST[$field] = $criteria[$field];
                        }
                    }
                }

                // Set Radius / Polygon Search
                if (!empty($criteria['radius'])) {
                    $_REQUEST['map']['radius']  = $criteria['radius'];
                }
                if (!empty($criteria['polygon'])) {
                    $_REQUEST['map']['polygon'] = $criteria['polygon'];
                }
            }
        }
        if (isset($criteria['map']['open'])) $_REQUEST['map']['open'] = $criteria['map']['open'];

        // Snippet, Over-Ride Feed Defaults
        $_REQUEST['search_city'] = isset($_REQUEST['search_city']) ? $_REQUEST['search_city'] : array();
        $_REQUEST['search_type'] = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : array();
    }

    // Select IDX to Search (set as active idx)
    if (!empty($search['idx'])) {
        // Slugify
        $_REQUEST['feed'] = $search['idx'];

        // Switch
        Util_IDX::switchFeed($_REQUEST['feed']);

        // IDX objects
        $idx = Util_IDX::getIdx();
        $db_idx = Util_IDX::getDatabase();
    }
}

// Load user's dismissed listings
$dismissed = $user->getDismissedListings($idx);

// Search View
if ((empty($_REQUEST['snippet']) || !empty($_REQUEST['sortorder']) || !empty($_REQUEST['p'])) || !empty($_REQUEST['refine']) || !empty($_REQUEST['price_range'])) {
    $_REQUEST['view'] = !empty($_COOKIE['results-view']) ? $_COOKIE['results-view'] : $_REQUEST['view'];
}
$_REQUEST['view'] = !empty($_REQUEST['view']) ? $_REQUEST['view'] : $search['view'];

// Mapping Disabled, Default to Grid
if (empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) && $_REQUEST['view'] == 'map') {
    $_REQUEST['view'] = 'grid';
}

// Search Order
if (!empty($_REQUEST['sortorder'])) {
    list ($_REQUEST['sort'], $_REQUEST['order']) = explode('-', $_REQUEST['sortorder']);
}

// Order By
$_REQUEST['order'] = !empty($_REQUEST['order']) ? $_REQUEST['order'] : $idx->getSearchOrder();
$search_order = $_REQUEST['order'];
$idx->setSearchOrder($search_order);

// Sort Direction
$_REQUEST['sort'] = !empty($_REQUEST['sort'])  ? $_REQUEST['sort']  : $idx->getSearchSort();
$search_sort = $_REQUEST['sort'];
$idx->setSearchSort($search_sort);

// Sort Order
if (!empty($_REQUEST['sort']) && !empty($_REQUEST['order'])) {
    $_REQUEST['sortorder'] = $_REQUEST['sort'] . '-' . $_REQUEST['order'];
}

// Snippet Price Range
if (!empty($_REQUEST['snippet']) && strpos(Http_Uri::getUri(), '/blog/') !== 0) {
    // Get Price Ranges
    $price_range = get_price_range($_REQUEST['price_range']);
    $_REQUEST['snip_minimum_price'] = $_REQUEST['minimum_price'];
    $_REQUEST['snip_maximum_price'] = $_REQUEST['maximum_price'];
    if (!empty($price_range['minimum_price']) || !empty($price_range['maximum_price'])) {
        $_REQUEST['minimum_price'] = $price_range['minimum_price'];
        $_REQUEST['maximum_price'] = $price_range['maximum_price'];
    }

    // Snippet title
    $snippet_title = snippet_title($_REQUEST['snippet_title']);
    if (!empty($snippet_title)) {
        // Print Heading
        echo '<h2 class="snippet-title">' . $snippet_title . '</h2>';
        $page_title = $snippet_title;
    }
}

// Build Query
$search_vars        = $idx->buildWhere($idx, $db_idx, 't1');
$search_where       = $search_vars['search_where'];
$search_title       = $search_vars['search_title'];

// Set Page Title
if (!empty($search_title) && empty($page_title) && empty($snippet_title)) {
    $page_title = strip_tags($search_title);
}

/**
 * Map Queries
 */

// WHERE Queries
$search_where = !empty($search_where) ? array($search_where) : array();

// HAVING Queries
$search_having = array();

// Search Group
$search_group = array();

// Latitude / Longitude Columns
$col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
$col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

// Search in Bounds
if (!empty($_REQUEST['map']['bounds']) && Settings::getInstance()->IDX_FEED != 'cms') {
    $bounds = $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'], $search_group, $col_latitude, $col_longitude);
}

// Search in Radiuses
$radiuses = $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);

// Search in Polygons
$polygons = $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
if (!empty($polygons)) {
    $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
}

// Add to Search Criteria
$sql_mapping = false;
if (!empty($search_group)) {
    $sql_mapping = '(' . implode(' OR ', $search_group) . ')';
    $search_where[] = $sql_mapping;
    if (!empty($search_having)) {
        $sql_mapping .= ' HAVING ' . implode(' OR ', $search_having);
    }
}

/**
 * Dismissed Listings
 */
$sql_exclude = false;
if (!empty($dismissed)) {
    // Exclude listing from results
    // as long as it's not being searched for
    // (via search_location / search_mls)
    $excluded = array_map(function ($mls_number) {
        if (is_array($_REQUEST['search_location']) && in_array($mls_number, $_REQUEST['search_location'])) {
            return null;
        }
        if (is_array($_REQUEST['search_mls']) && in_array($mls_number, $_REQUEST['search_mls'])) {
            return null;
        }
        return $mls_number;
    }, $dismissed);

    // Excluded query string
    if (!empty($excluded)) {
        $sql_exclude = "`t1`.`" . $idx->field('ListingMLS') . "` NOT IN ('" . implode("', '", $excluded) . "')";
        $search_where[] = $sql_exclude;
    }
}

/**
 * Price Range Links for IDX Snippets
 */
if (!empty($_REQUEST['snippet']) && strpos(Http_Uri::getUri(), '/blog/') !== 0) {
    if (!empty($_REQUEST['snippet_price_table'])) {
        echo price_range_table($sql_mapping, $sql_exclude);
    }
}

/**
 * Search Query
 */

// Query String (WHERE & HAVING)
$search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

// Count Search Query
if (empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) || empty($polygons)) {
    $search_count_query = "SELECT SQL_CACHE COUNT(*) AS `total` FROM `" . $idx->getTable() . "` `t1`" . (!empty($search_where) ? ' WHERE ' . $search_where : '');
} else {
    if ($idx->getLink() == 'cms') {
        $search_count_query = "SELECT SQL_CACHE COUNT(*) AS `total` FROM `" . $idx->getTable() . "` `t1`" . (!empty($search_where) ? ' WHERE ' . $search_where : '');
    } else {
        $search_count_query = "SELECT SQL_CACHE COUNT(*) AS total"
            . " FROM (SELECT `t1`.`" . $idx->field('ListingMLS') . "` AS `total`, `t2`.`Point`"
                . " FROM `" . $idx->getTable() . "` `t1`"
                . " JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
                . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
                . (!empty($search_where) ? ' WHERE ' . $search_where : '')
            . ") as listings";
    }
}

// Execute Count Query
$search_results_count = $db_idx->fetchQuery($search_count_query);

// MLS Compliance, Search Result Limit
if (isset($_COMPLIANCE['limit']) && ($search_results_count['total'] >= $_COMPLIANCE['limit'])) {
    $results_limit = $_COMPLIANCE['limit'];
} else {
    $results_limit = $search_results_count['total'];
}

// Current Page
$current_page = isset($_REQUEST['p']) && ($_REQUEST['p'] > 0) ? $_REQUEST['p'] : 1;
$idx->setPage($current_page);

// Page Limit
$page_limit = !empty($_REQUEST['page_limit']) ? $_REQUEST['page_limit'] : $idx->getPageLimit();
$idx->setPageLimit($page_limit);

// HTTP Query, REQUEST_URI
$query_string = array();
if (strpos($_SERVER['REQUEST_URI'], '?')) {
    parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_string);
    unset($query_string['auto_save']);
    unset($query_string['view']);
    unset($query_string['p']);
}

// Pagination
$pagination = generate_page_bar($results_limit, $idx->getPage(), $idx->getPageLimit(), '', '', $query_string);

// Max Page Exceeded, Show Last Page, Send 404
if ($pagination['pages'] > 0 && $_REQUEST['p'] > $pagination['pages']) {
    $idx->setPage($current_page = $pagination['pages']);
    $pagination = generate_page_bar($results_limit, $idx->getPage(), $idx->getPageLimit(), '', '', $query_string);
    $page->info('link.canonical', '');
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);

// Invalid Page Requested, Show First Page, Send 404
} else if (isset($_REQUEST['p']) && $_REQUEST['p'] < 1) {
    $idx->setPage($current_page = 1);
    $pagination = generate_page_bar($results_limit, $idx->getPage(), $idx->getPageLimit(), '', '', $query_string);
    $page->info('link.canonical', '');
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
}

// Set Prev/Next
if (!empty($pagination['prev'])) {
    $page->info('link.prev', $pagination['prev']['url']);
}
if (!empty($pagination['next'])) {
    $page->info('link.next', $pagination['next']['url']);
}

// Calculate SQL Limit
if ($results_limit > $page_limit) {
    $limitvalue = ($current_page * $page_limit) - $page_limit;
    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
    $true_limit = ($limitvalue + $page_limit) > $results_limit ? ($results_limit - $limitvalue) : $page_limit;
    $sql_limit  = " LIMIT " . $limitvalue . "," . $true_limit;
} else {
    $sql_limit = '';
}

// Order By Query
if (!empty($search_order) && !empty($search_sort) && !is_null($idx->field($search_order))) {
    // Resolve search field. If it is timestamp_created, allow the naked field to be used as it
    // always exists (last-ditch chance to search by newest listings first)
    $sort_col = $idx->field($search_order) ? $idx->field($search_order) : ($search_order == 'timestamp_created' ? $search_order : false);
    if (!empty($sort_col)) {
        $sql_order = " ORDER BY `t1`.`" . $sort_col . '` ' . $search_sort;
    }
}

// Order by ID
$sql_order .= (!empty($sql_order) ? "," : " ORDER BY") . " `t1`.`id` "
    . (!empty($search_sort) ? $search_sort : "ASC");

// Require mapping data
$mapping = !empty($polygons); // && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']);

// Build search query
$search_query = "SELECT SQL_CACHE " . $idx->selectColumns("`t1`.");
$search_query .= " FROM `" . $idx->getTable() . "` `t1`";
$search_query .= $mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "";
$search_query .= !empty($search_where) ? " WHERE " . $search_where : "";

// Search query to execute
$search_query_exec = $search_query . $sql_order . $sql_limit;

// Execute Search Query
$search_results = $db_idx->query($search_query_exec);

// Store Search Query
$_SESSION['last_search'] = $search_query_exec;

/**
 * Save Recently Viewed Search
 */
$backend_user = Auth::get();
if ($user->isValid() && !$backend_user->isValid()) {
    // Refined Search
    if (!empty($_REQUEST['refine']) || !empty($_REQUEST['quick_search'])) {
        // Searchable IDX Fields
        $search_fields = search_fields($idx);
        $search_fields = array_keys($search_fields);
        $search_fields = array_merge(array('map', 'refine', 'search_location'), $search_fields);

        // Search Criteria
        $criteria = array();

        // Build Search Criteria
        $request = $_REQUEST;
        foreach ($search_fields as $field) {
            if (is_array($request[$field])) {
                foreach ($request[$field] as $k => $v) {
                    if (empty($v)) {
                        unset($request[$field][$k]);
                    }
                }
            }
            if (!empty($request[$field])) {
                $criteria[$field] = $request[$field];
            }
        }

        // Remove Un-Needed Map Criteria
        if (!empty($criteria['map'])) {
            unset($criteria['map']['longitude'], $criteria['map']['latitude'], $criteria['map']['zoom']);
            if (empty($criteria['map']['bounds'])) {
                unset($criteria['map']['ne']);
                unset($criteria['map']['sw']);
            }
            if (empty($criteria['map'])) {
                unset($criteria['map']);
            }
        }

        // Serialize
        $criteria = serialize($criteria);

        // Check if Viewed Search is Saved
        $viewed_search = $db_users->fetchQuery("SELECT `id` FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE "
                                             . "`user_id`  = '" . $user->user_id() . "' AND "
                                             . "`criteria` = '" . $db_users->cleanInput($criteria) . "' AND "
                                             . "`table`    = '" . $db_users->cleanInput($idx->getTable()) . "' AND "
                                             . "`idx`      = '" . $db_users->cleanInput($idx->getName()) . "'"
                                             . ";");

        // New Viewed Search
        if (empty($viewed_search)) {
            // Search URL
            $query = array();
            if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
                parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query);
                $query = array_filter($query);
                unset($query['p']);
            }

            //Set feed in search URL
            if (!empty($query['idx'])) {
                $query['feed'] = $query['idx'];
                unset($query['idx']);
            }

            $search_url = http_build_query($query);

            // Build INSERT Query
            $query = "INSERT INTO `" . TABLE_VIEWED_SEARCHES . "` SET "
                   . "`user_id`   = '" . $user->user_id() . "', "
                   . "`title`     = '" . $db_users->cleanInput($search_title)        . "', "
                   . "`criteria`  = '" . $db_users->cleanInput($criteria)        . "', "
                   . "`table`     = '" . $db_users->cleanInput($idx->getTable()) . "', "
                   . "`idx`       = '" . $db_users->cleanInput($idx->getName())  . "', "
                   . "`url`       = '" . $db_users->cleanInput($search_url)      . "', "
                   . "`timestamp` = NOW();";

            // Execute Query
            if ($db_users->query($query)) {
                // Log Event: Lead Performed Search
                $event = new History_Event_Action_ViewedSearch(array(
                    'search' => $db_users->fetchQuery("SELECT * FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `id` = '" . $db_users->insert_id() . "';")
                ), array(
                    new History_User_Lead($user->user_id())
                ));

                // Save to DB
                $event->save();

                // Increment Performed Searches
                $db_users->query("UPDATE `" . TABLE_USERS . "` SET `num_searches` = `num_searches` + 1 WHERE `id` = '" . $user->user_id() . "';");

                // Run hook
                Hooks::hook(Hooks::HOOK_LEAD_SEARCH_PERFORMED)->run($user->getRow(), $idx, unserialize($criteria), html_entity_decode($search_title, ENT_COMPAT | ENT_HTML401, 'UTF-8'));
            }
        } else {
            // Build UPDATE Query
            $query = "UPDATE `" . TABLE_VIEWED_SEARCHES . "` SET `views` = `views` + 1, `timestamp` = NOW() WHERE `id` = '" .  $viewed_search['id'] . "';";

            // Execute Query
            $db_users->query($query);
        }
    }
}

// Search Results
$results = array();

// Map Markers
$markers = array();

// DriveTime marker
if (!empty($settings->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', $settings->ADDONS)) {
    $drive_time_marker = true;
}

// Listing Results Found
if (!empty($search_results_count['total'])) {
    // Load user's saved favorites
    $bookmarked = $user->getSavedListings($idx);

    // Process Search Results
    while ($search_result = $db_idx->fetchArray($search_results)) {
        // Parse Listing
        $result = Util_IDX::parseListing($idx, $db_idx, $search_result);

        // Add to Collection
        $results[] = $result;

        // Require Co-Ords
        if (intval($result['Latitude']) != 0 && intval($result['Longitude']) != 0) {

            // Determine if we Need a Separate Drive Time Map Marker
            if ($drive_time_marker === true) {
                $drive_time_marker = !DriveTime::checkMapMarkerDuplicate($_REQUEST['dt_address'], $result['Address']);
            }

            // Tooltip HTML
            ob_start();
            $listing_tooltip = $result;
            include $page->locateTemplate('idx', 'misc', 'tooltip');
            $tooltip = str_replace(array("\r\n", "\n", "\t"), "", ob_get_clean());

            // Add Marker
            $markers[] = array(
                //'title' => implode(', ', array($result['Address'], $result['AddressCity'], $result['AddressState'])) . html_entity_decode(' MLS&reg; #', ENT_COMPAT | ENT_HTML401, 'UTF-8') . $result['ListingMLS'],
                'tooltip' => $tooltip,
                'lat' => $result['Latitude'],
                'lng' => $result['Longitude']
            );
        }

        // Return Maximum Results
        if (count($results) >= $page_limit) {
            break;
        }
    }

    // Set Unique Description
    $first = $results[0];
    $last  = $results[count($results) - 1];
    $meta_desc = $first['Address'] . ', ' . $first['AddressCity']. ' ' . $first['AddressState'] . ' ' . $first['AddressZipCode'] . ' to ' . $last['Address'] . ', ' . $last['AddressCity']. ' ' . $last['AddressState'] . ' ' . $last['AddressZipCode'];

    // Free Result
    if ($search_results) {
        $search_results->close();
    }
}

// Save Search
if (!empty($saved_search)) {
    // Update Search Title
    $saved_search['title'] = isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : $saved_search['title'];

    // Update Search Frequency
    $saved_search['frequency'] = isset($_REQUEST['frequency']) ? $_REQUEST['frequency'] : $saved_search['frequency'];

    // Set $_REQUEST['search_title']
    $_REQUEST['search_title'] = $saved_search['title'];

    // Set $_REQUEST['frequency']
    $_REQUEST['frequency'] = $saved_search['frequency'];
} else {
    // Save Search String
    $_REQUEST['save_prompt'] = isset($search_title) ? $search_title : '';
}

// Append Page # to Page Title
if (is_numeric($current_page) && $current_page > 1) {
    $page_title .= (!empty($page_title) ? ' - ' : '') . 'Page #' . number_format($current_page);
    $meta_desc   = 'Page #' . number_format($current_page) . ' of Listings' . (!empty($meta_desc) ? ': ' . $meta_desc : '');
}

// Append Sort Order to Page Title
if (isset($_GET['sortorder']) && !empty($_GET['sortorder'])) {
    foreach (IDX_Builder::getSortOptions(true) as $sortOption) {
        if ($sortOption['value'] == $_GET['sortorder']) {
            $page_title .= ' (' . $sortOption['page_title'] . ')';
            break;
        }
    }
}

// Meta Description
$meta_desc = !empty($meta_desc) ? $meta_desc : '';

// Back to Search Form
$search_form = $user->info('search_form');

// Find "Result" TPL
$result_tpl = $page->locateTemplate('idx', 'misc', 'result');

// Query String Details
$query_string = array();
$querystring_nosort = array();
if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
    parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_string);
    // unset vars we dont want carried over
    unset($query_string['search_title']);
    // query string w/o search order info
    $querystring_nosort = $query_string;
    unset($querystring_nosort['sortorder']);
    unset($querystring_nosort['sort']);
}

// Searchable IDX Fields
$searchable = array_merge(array_map(function ($field) {
    return $field['form_field'];
}, search_fields($idx)), array_merge([
    'map', 'order', 'sort', 'view', 'lead_id', 'create_search', 'edit_search', 'save_prompt', 'search_title', 'search_location', 'frequency', 'saved_search_id', 'suggested'
], DriveTime::IDX_FORM_FIELDS));

// Current Search Criteria
$criteria = array();
array_walk($_REQUEST, function ($v, $k) use (&$criteria, $searchable) {
    if (!empty($v) && in_array($k, $searchable)) {
        $criteria[$k] = $v;
    }
});

// Extra Search Criteria
$criteria = array_merge($criteria, array(
    'feed'         => $idx->getLink(),
    'save_prompt'  => strip_tags($criteria['save_prompt']),
    'search_title' => strip_tags($criteria['search_title'])
));

// Search View
$view = in_array($_REQUEST['view'], array('map', 'grid', 'detailed')) ? $_REQUEST['view'] : 'grid';

// Ensure only numeric values
$_REQUEST['map']['longitude']   = (float) $_REQUEST['map']['longitude'];
$_REQUEST['map']['latitude']    = (float) $_REQUEST['map']['latitude'];
$_REQUEST['map']['zoom']        = (int) $_REQUEST['map']['zoom'];
$_REQUEST['p']                  = (int) $_REQUEST['p']? : 1;

// Build the Drive Time Marker if we Need it
if (!empty($drive_time_marker)
    && !empty($_REQUEST['place_lat'])
    && !empty($_REQUEST['place_lng'])
    && !empty($_REQUEST['dt_address'])
){
    // Add Marker
    $markers[] = [
        'title' => $_REQUEST['dt_address'],
        'lat' => $_REQUEST['place_lat'],
        'lng' => $_REQUEST['place_lng'],
        'icon' => sprintf('%smap/marker-home@2x.png', $settings->SETTINGS['URL_IMG'])
    ];
}

if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    // Map Options
    $mapOptions = array(
        'init' => false,
        // Streetview
        'streetview' => !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
        // Zoom
        'zoom' => !empty($_REQUEST['map']['zoom']) ? intval($_REQUEST['map']['zoom']) : intval(Settings::getInstance()->SETTINGS['map_zoom']),
        // Center
        'center' => array(
            'lat' => !empty($_REQUEST['map']['latitude'])  ? floatval($_REQUEST['map']['latitude'])  : floatval(Settings::getInstance()->SETTINGS['map_latitude']),
            'lng' => !empty($_REQUEST['map']['longitude']) ? floatval($_REQUEST['map']['longitude']) : floatval(Settings::getInstance()->SETTINGS['map_longitude'])
        ),
        // Manager
        'manager' => array(
            // Markers
            'markers' => $markers,
            // Bounds
            'bounds' => empty($polygons) && empty($radiuses) && empty($_REQUEST['map']['bounds'])
        ),
        // Polygon Searches
        'polygons' => !empty($polygons) ? array_map(function ($polygon) {
            return array_map(function ($point) {
                list ($lat, $lng) = explode(' ', $point);
                return array('lat' => floatval($lat), 'lng' => floatval($lng));
            }, explode(',', $polygon));
        }, $polygons) : null,
        // Radius Searches
        'radiuses' => !empty($radiuses) ? array_map(function ($radius) {
            list ($lat, $lng, $radius) = explode(',', $radius);
            return array(
                'radius' => $radius,
                'lat' => floatval($lat), 'lng' => floatval($lng),
                'edit' => empty($_REQUEST['snippet'])
            );
        }, $radiuses) : null
    );

    // Drive Time - Center and Zoom Map Around Drive Time Results
    if (!empty($drive_time_marker)) {
        if (!empty($_REQUEST['place_lat']) && !empty($_REQUEST['place_lng'])) {
            $mapOptions['center']['lat'] = (float) $_REQUEST['place_lat'];
            $mapOptions['center']['lng'] = (float) $_REQUEST['place_lng'];
        }
        if (!empty($_REQUEST['place_zoom'])) {
            $mapOptions['zoom'] = (int) $_REQUEST['place_zoom'];
        }
    }

    // Require map javascript
    $page->getSkin()->loadMapApi();
}

// JS variables
$page->addJavascript(
    sprintf('var feed = feed || "%s";', $idx->getLink()) .
	sprintf('var view = view || %s;', json_encode($view)) .
	sprintf('var criteria = criteria || Array(); criteria["%s"] = %s;', $criteria['feed'], json_encode($criteria)) .
    sprintf('var mapOptions = mapOptions || Array(); mapOptions["%s"] = %s', $criteria['feed'], json_encode($mapOptions))
, 'dynamic', false);

// Search results javascript
if (!Skin::hasFeature(Skin::PROVIDES_SEARCH_RESULTS_JS)) {
    $page->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/results.js', 'page');
}

if (Settings::isREW() && isset($_REQUEST['debug'])) {
    echo '<pre>' . $search_count_query . '</pre>';
    echo '<pre>' . $search_query_exec . '</pre>';
}
