<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes in part from a cooperative data exchange program of the Realtor&reg; Association of Martin County, Inc. MLS. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the IDX logo (Broker Reciprocity) or name and detailed information about such listings includes the name of the listing brokers. Data provided is deemed reliable but is not guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use, that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';

	if (in_array($_GET['load_page'], array('details', 'brochure'))) {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' Realtor&reg; Association of Martin County, Inc. MLS. All rights reserved. </p>';
	}

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Martin County MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ramc-th.jpg" border="0" width="95" height="35" style="width:95px; height:35px;" />'; // width and height are min requirements

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
