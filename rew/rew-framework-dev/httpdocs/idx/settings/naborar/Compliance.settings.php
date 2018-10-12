<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'Copyright&copy; ' . date('Y') . ' Northwest Arkansas Board of REALTORS&reg;. All rights reserved.';
	$_COMPLIANCE['disclaimer'][] = ' All information provided by the listing broker is deemed reliable but is not guaranteed and should be independently verified.';
	$_COMPLIANCE['disclaimer'][] = ' Information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.<br /> ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}


// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Search Results, Display Provider Above View Listing Button
$_COMPLIANCE['results']['show_above_actions'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));

$_COMPLIANCE['details']['lang']['provider'] = 'Listing Office: ';
