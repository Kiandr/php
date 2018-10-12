<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . 'Copyright &copy; 2016 Lakes Country Association of REALTORS. All rights reserved. All'
        . ' information is being provided exclusively for consumers\' personal, non-commercial use'
        . ' and may not be used for any purpose other than to identify prospective properties'
        . ' consumers may be interested in purchasing. The data is deemed reliable but is not'
        . ' guaranteed accurate by the MLS.'
        . '</p>';

}

// Listing agent and Listing office required on all listings in all views.
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['show_agent'] = true;
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('local','map'));
$_COMPLIANCE['details']['show_agent'] = !in_array($_GET['load_page'], array('local','map'));
