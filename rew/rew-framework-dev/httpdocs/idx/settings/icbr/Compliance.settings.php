<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . 'Copyright &copy; 2016 Itasca County Board of Realtors. All rights'
        . ' reserved. All information is being provided exclusively for'
        . ' consumers\' personal, non-commercial use and may not be used for'
        . ' any purpose other than to identify prospective properties'
        . ' consumers may be interested in purchasing. The data is deemed'
        . ' reliable but is not guaranteed accurate by the MLS.'
        . '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));
