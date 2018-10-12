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
		. 'Copyright &copy; ' . date('Y') . ' Cedar Rapids Area Association of REALTORS&reg;. All rights reserved. All information provided by the listing agent/broker is deemed reliable but is not guaranteed and should be independently verified. Information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.'
		. '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions'));

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;
// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = !in_array($_GET['load_page'], array('map', 'directions'));
