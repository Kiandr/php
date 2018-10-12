<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information is provided exclusively for consumers\' personal, non-commercial use, and may not be used for any purpose other than to identify prospective properties that a consumer may be interested in purchasing. All Information believed to be reliable but not guaranteed and should be independently verified. &copy; ' . date('Y') . ' Metropolitan Indianapolis Board of REALTORS&reg;. All rights reserved.</p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'map' ? true : false);
