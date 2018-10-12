<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '<p>Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?> and updating occurs up to every 15 minutes.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p>The information being provided by the Lawton Board of REALTORS&reg; is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p>Copyright&copy; Lawton Board of REALTORS&reg;. All Rights Reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = 'LawtonBOR';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
