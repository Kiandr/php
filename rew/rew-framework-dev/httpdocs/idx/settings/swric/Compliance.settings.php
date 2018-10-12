<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    $broker_name = '[INSERT BROKER NAME]';
	$firm_name = '[INSERT FIRM NAME]';

    // Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' MichRIC, LLC. All rights reserved. Information Deemed Reliable But Not Guaranteed</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Custom MLS Compliance fields (Office & Agent Phone)
$_COMPLIANCE['details']['extra'] = function ($idx, $db_idx, $listing, $_COMPLIANCE) {
	return array(
		array(
			'heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ?
				$_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'),
			'fields' => array(
				!empty($_COMPLIANCE['details']['show_agent']) ?
					array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
				!empty($_COMPLIANCE['details']['show_office']) ?
					array('title' => (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'), 'value' => 'ListingOffice') : null,
			),
		),
	);
};

// Show disclaimer above the inquire form
$_COMPLIANCE['details']['above_inquire'] = true;
