<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer']   = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    // MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = 'DISCLAIMER: Information Deemed Reliable but not guaranteed. The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.<br />';
    $_COMPLIANCE['disclaimer'][] = 'All listings are provided courtesy of the Emerald Coast Association of Realtors, Copyright' . date('Y'). ', All rights reserved.';
    $_COMPLIANCE['disclaimer'][] = '</p>';

}

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye'));