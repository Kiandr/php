<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"> Listing information'
        . ' provided courtesy of the Longview Area Association of Realtors. IDX'
        . ' information is provided exclusively for consumers\' personal,'
        . ' non-commercial use, and it may not be used for any purpose other than'
        . ' to identify prospective properties consumers may be interested in purchasing.'
        . ' The data is deemed reliable, but is not guaranteed accurate by the MLS.</p>';
}

if (!empty(Settings::getInstance()->SETTINGS['registration'])) {
    // Search Results, Display Office Name
    $_COMPLIANCE['results']['show_office'] = function ($listing) {
        return strlen($listing['ListingRemarks']) > 200;
    };

    // Listing Details, Display Office Name
    $_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));
}
