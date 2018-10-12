<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the Internet Data exchange (IDX) program of the Medicine Hat Real Estate Board. ';
	$_COMPLIANCE['disclaimer'][] = 'IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. ';
	$_COMPLIANCE['disclaimer'][] = 'Real estate listings held by brokerage firms other than ' . $broker_name . ' , are indicated by detailed information about them such as the name of the listing firms. Information deemed reliable but not guaranteed. ';
	$_COMPLIANCE['disclaimer'][] = 'Copyright 2016 Medicine Hat Real Estate Board. All Rights Reserved. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));

$_COMPLIANCE['results']['lang']['provider'] = 'Listing Courtesy of: ';
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of: ';
