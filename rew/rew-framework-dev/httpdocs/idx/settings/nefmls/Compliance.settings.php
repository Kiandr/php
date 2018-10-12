<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Require Account Data
	$broker_name = '[BROKER]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange (IDX) program of the Northeast Florida Multiple Listing Service, Inc. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the listing broker\'s name and detailed information about such listings includes the name of the listing brokers. Information deemed reliable but not guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy;' . date('Y') . ' Northeast Florida Multiple Listing Service, Inc. All rights reserved. </p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
