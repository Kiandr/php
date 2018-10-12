<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this website comes from the Baldwin County Association Of REALTORS&reg;. The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information deemed reliable but not guaranteed.</p>';

}

// Listing Details and Results: Display Office Name
$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'map');
$_COMPLIANCE['results']['show_office'] = true;
