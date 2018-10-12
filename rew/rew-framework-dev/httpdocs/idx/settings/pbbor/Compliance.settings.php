<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes in part from a cooperative data exchange program of Palm Beach Board of Realtors Multiple Listing Service. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the listing broker\'s logo or name and detailed information about such listings includes the name of the listing brokers. Data provided is deemed reliable but is not guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The broker providing this data believes them to be correct, but advises interested parties to confirm them before relying on them in a purchase decision. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy; ' . date('Y') . ' Palm Beach Board of Realtors Multiple Listing Service. All Rights Reserved. </p>';


	// Recommended Disclaimer If Not Displaying All Listings in Feed (use one)
	// $firm_name = '[INSERT FIRM NAME]';
	// $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">' . $firm_name . ' participates in the Palm Beach Board of Realtors Multiple Listing Service data exchange program, allowing us to display other borkers's listings on our site. However, ' . $firm_name . ' displays only ' . $account_displaying . '. </p>';

	// $account_displaying = '[listings in Palm Beach][only condominium listings][exceptional properties (with list prices above $500,000)][other]';
	// $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">' . $firm_name . ' does not display the entire Palm Beach Board of Realtors Mutiple Listing data exchange program database on this web site. The listings of some real estate brokerage firms have been excluded. </p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
