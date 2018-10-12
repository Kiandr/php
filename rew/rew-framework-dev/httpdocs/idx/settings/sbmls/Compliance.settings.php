<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Require Account Data
	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes from the Internet Data Exchange Program of the Santa Barbara Multiple Listing Service. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the "MLS" logo and detailed information about them includes the name of the listing brokers. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information is deemed reliable, but is not guaranteed. All properties are subject to prior sale, change or withdrawal. Neither listing broker(s) nor ' . $broker_name . ' shall be responsible for any typographical errors, misinformation, or misprints. &copy;' . date('Y') . ' Santa Barbara Multiple Listing Service. All rights reserved. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use, that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
