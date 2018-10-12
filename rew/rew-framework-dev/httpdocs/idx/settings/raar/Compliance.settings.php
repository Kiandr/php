<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The data relating to real estate for sale on this website comes in part from the MLS of the Rockford Area Association of REALTORS&reg;. All information is deemed reliable but not guaranteed and should be independently verified. Listing information from this property search is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.'
		. '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));

// Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = !in_array($_GET['load_page'], array('map', 'local'));
