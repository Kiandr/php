<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listings provided courtesy of the Greater Greenville Association of REALTORS&reg;, Copyright ' . date('Y') . '.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (!in_array($_GET['load_page'], array('map', 'birdseye', 'streetview')))? true : false;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (!in_array($_GET['load_page'], array('map', 'birdseye', 'streetview')))? true : false;
