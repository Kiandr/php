<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    // Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright &copy; ' . date('Y') . ', Houston Realtors Information Service, Inc. All information provided is deemed reliable but is not guaranteed and should be independently verified. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The IDX information is provided exclusively for consumers\' personal, non-commercial use, it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. All information provided is deemed reliable but is not guaranteed accurate by the MLS and should be independently verified.</p>';

}

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = false;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = false;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local')) ? true : false;
