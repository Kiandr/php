<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
//$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'NSAR IDX Reciprocity listings are displayed in accordance with NSAR\'s IDX Agreements and property information is provided under copyright&copy; by the Nova Scotia Association of REALTORS&reg;. ';
	$_COMPLIANCE['disclaimer'][] = 'The above information is from sources deemed reliable but it should not be relied upon without independent verification.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Search Results: Display MLS #
$_COMPLIANCE['results']['show_mls'] = true;
