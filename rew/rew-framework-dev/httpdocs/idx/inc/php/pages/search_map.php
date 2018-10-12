<?php

// IDX Mapping Not Enabled, Re-Direct
if (empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    header('Location: /idx/', true, 301);
    exit;
}

// Remember this Page
$user->setBackUrl($_SERVER['REQUEST_URI']);
$user->setSearchUrl($_SERVER['REQUEST_URI']);

// Agent, Create / Edit Saved Search
if ((!empty($_REQUEST['create_search']) || !empty($_REQUEST['edit_search'])) && !empty($_REQUEST['lead_id'])) {
    global $lead;
    $lead = $db_users->fetchQuery("SELECT `id`, `first_name`, `last_name` FROM `" . TABLE_USERS . "` WHERE `id` = '" . $db_users->cleanInput($_REQUEST['lead_id']) . "';");
}

// Load Saved Search
if (!empty($_REQUEST['saved_search_id'])) {
    // Select IDX Search
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `id` = '" . $db_users->cleanInput($_REQUEST['saved_search_id']) . "'");

    // Saved Search
    global $saved_search;
    $saved_search = $search;

// Load Custom Search
} else if (!empty($_REQUEST['search_id'])) {
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_SEARCHES . "` WHERE MD5(`id`) = '" . $db_users->cleanInput($_REQUEST['search_id']) . "'");

// Load Defaults
} else {
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '" . $db_users->cleanInput(Settings::getInstance()->IDX_FEED) . "';");
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

    // Search Criteria
    if (!empty($search['criteria'])) {
        // Searchable Fields
        $search_fields = search_fields($idx);
        $search_fields = array_keys($search_fields);
        $search_fields = array_merge(array('map', 'view', 'search_location'), $search_fields);

        // Set Search Criteria
        $criteria = unserialize($search['criteria']);
        if (!empty($criteria) && is_array($criteria)) {
            foreach ($search_fields as $field) {
                if (isset($criteria[$field])) {
                    if (!isset($_REQUEST[$field])) {
                        $_REQUEST[$field] = $criteria[$field];
                    }
                }
            }
        }

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

// Search By Map
$_REQUEST['search_by'] = 'map';

// Search Order
if (!empty($_REQUEST['sortorder'])) {
    list ($_REQUEST['sort'], $_REQUEST['order']) = explode('-', $_REQUEST['sortorder']);
}

// Build Query, Get Search Title
$search_vars = $idx->buildWhere($idx, $db_idx, 't1');
$search_title = $search_vars['search_title'];

// Sort Order
if (!empty($_REQUEST['sort']) && !empty($_REQUEST['order'])) {
    $_REQUEST['sortorder'] = $_REQUEST['sort'] . '-' . $_REQUEST['order'];
}

// Save Search Title
if (empty($saved_search)) {
    // Save Search String
    $_REQUEST['save_prompt'] = isset($search_title) ? $search_title : '';

    // Set Page Title
    if (!empty($search_title)) {
        $page_title = strip_tags($search_title);
    }
}

// Find "Search By" TPL
$search_by_tpl = $page->locateTemplate('idx', 'misc', 'search_by');

// Search Radiuses
$radiuses = false;
if (!empty($_REQUEST['map']['radius']) && is_string($_REQUEST['map']['radius'])) {
    $radiuses = json_decode($_REQUEST['map']['radius'], true); // Parse as JSON Array
}

// Search Polygons
$polygons = false;
if (!empty($_REQUEST['map']['polygon']) && is_string($_REQUEST['map']['polygon'])) {
    $polygons = json_decode($_REQUEST['map']['polygon'], true); // Parse as JSON Array
    if (json_last_error() != JSON_ERROR_NONE) {
        $polygons = array($_REQUEST['map']['polygon']); // Backwards Compatibility: Not JSON Array, Single Polygon Only
    }
}

// Map Options
$mapOptions = array(
    // Restore State
    'restore' => true,
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
        // Bounds
        'bounds' => empty($polygon) && empty($radiuses) && empty($_REQUEST['map']['bounds']),
        // Markers
        'markers' => $markers
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
            'edit' => !empty($_REQUEST['snippet'])
        );
    }, $radiuses) : null
);

// Require map javascript
$page->getSkin()->loadMapApi();

// Map search variables
$page->addJavascript('
	var complianceLimit = complianceLimit || ' . intval($_COMPLIANCE['limit']) . ';
	var mapOptions = mapOptions || ' . json_encode($mapOptions) . ';
', 'dynamic', false);

// Map search javascript
if (!Skin::hasFeature(Skin::PROVIDES_SEARCH_MAP_JS)) {
    $page->addJavascript($_SERVER['DOCUMENT_ROOT'] .  '/inc/js/idx/search_map.js', 'page');
}
