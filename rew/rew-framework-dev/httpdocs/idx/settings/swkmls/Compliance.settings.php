<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this Web Site comes in part from the IDX Program of the Southwest Kansas Board Of REALTORS&reg; Multiple Listing Service. &copy; Copyright ' . date("Y") . '. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';

}

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

if (!in_array($_GET['load_page'], array('map','directions'))) {
	// Listing Details, Display Agent Name
	$_COMPLIANCE['details']['show_agent'] = true;

	// Listing Details, Display Office Name
	$_COMPLIANCE['details']['show_office'] = true;
}
