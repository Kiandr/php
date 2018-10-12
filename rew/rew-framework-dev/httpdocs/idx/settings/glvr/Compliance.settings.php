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
	$_COMPLIANCE['disclaimer'][] = 'Copyright &copy; 2016, Greater Lehigh Valley Association of Realtors MLS. ';
	$_COMPLIANCE['disclaimer'][] = 'All information provided is deemed reliable but is not guaranteed and should be independently verified. ';
	$_COMPLIANCE['disclaimer'][] = 'The IDX information is provided exclusively for consumers\' personal, non-commercial use, ';
	$_COMPLIANCE['disclaimer'][] = 'it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. ';
	$_COMPLIANCE['disclaimer'][] = 'All information provided is deemed reliable but is not guaranteed accurate by the MLS and should be independently verified. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

$_COMPLIANCE['details']['lang']['provider'] = 'Listing Office: ';

// Search Results Limit
$_COMPLIANCE['limit'] = 1000;
