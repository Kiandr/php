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
	$_COMPLIANCE['disclaimer'][] = 'The information being provided by the Tehama County Association of REALTORS&reg; is exclusively for consumers\' personal, non-commercial use ';
	$_COMPLIANCE['disclaimer'][] = 'and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. ';
	$_COMPLIANCE['disclaimer'][] = 'The data is deemed reliable but is not guaranteed accurate by the MLS. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_agent'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['details']['show_agent'] = true;

// Search Results, Display Provider Above View Listing Button
$_COMPLIANCE['results']['show_above_actions'] = true;

