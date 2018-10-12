<?php

// Save Search Form URL
$user->saveInfo('search_form', $_SERVER['REQUEST_URI']);

// Possible Search Types
$_REQUEST['search_by'] = in_array($_REQUEST['search_by'], array('city', 'subdivision', 'school', 'zip', 'mls')) ? $_REQUEST['search_by'] : 'city';

// Search by City
if (in_array($_REQUEST['search_by'], array('city', 'subdivision', 'school'))) {
    $city = IDX_Panel::get('City');
}

// Search Features
if (in_array($_REQUEST['search_by'], array('city', 'subdivision', 'zip', 'school'))) {
    // Search by Price Range
    $price = IDX_Panel::get('Price', array(
        'placeholderMinPrice' => 'No Minimum',
        'placeholderMaxPrice' => 'No Maximum',
        'placeholderMinRent' => 'No Minimum',
        'placeholderMaxRent' => 'No Maximum'
    ));

    // Search by Beds & Bathr
    $rooms = IDX_Panel::get('Rooms', array(
        'placeholderBeds' => 'No Preference',
        'placeholderBaths' => 'No Preference'
    ));

    // Search by Min. Sqft
    $sqft = IDX_Panel::get('Sqft', array(
        'minOption' => 'No Preference'
    ));

    // Search by Min. Acres
    $acres = IDX_Panel::get('Acres', array(
        'minOption' => 'No Preference'
    ));

    // Search by Min. Year Built
    $year = IDX_Panel::get('Year', array(
        'minOption' => 'No Preference'
    ));

    // Search by Property Type
    $type = IDX_Panel::get('Type');
}

// Load Search Defaults
$search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '" . Settings::getInstance()->IDX_FEED . "';");
if (empty($search)) {
    $search = $db_users->fetchQuery("SELECT * FROM `" . TABLE_IDX_DEFAULTS . "` WHERE `idx` = '';");
}

// Load Search
if (!empty($search)) {
    if (!empty($search['criteria']) && empty($_REQUEST['refine'])) {
        // Searchable Fields
        $search_fields = search_fields($idx);
        $search_fields = array_keys($search_fields);
        $search_fields = array_merge(array('map', 'view', 'search_location'), $search_fields);

        // Search Criteria
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
}

// Modify Search Request
$_REQUEST = search_criteria($idx, $_REQUEST);

// Find "Search By" TPL
$search_by_tpl = $page->locateTemplate('idx', 'misc', 'search_by');
