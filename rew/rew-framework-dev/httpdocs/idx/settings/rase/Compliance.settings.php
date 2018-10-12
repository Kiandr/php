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

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">';
	$_COMPLIANCE['disclaimer'][] .= '<img style="float: left; height: 49px; width: 95px; margin-right: 2px; " src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rase.jpg" border="0" />';
	$_COMPLIANCE['disclaimer'][] .= 'The data relating to real estate for sale on this web site comes in part from the'
		. 'Internet Data Exchange Program of the REALTOR&reg; Association of the Sioux Empire, Inc., Multiple Listing Service. '
		. 'Real estate listings held by brokerage firms other than ' . $broker_name
		. ' are marked with the Internet Data Exchange&trade; logo or the Internet Data Exchange thumbnail logo (a little black house) and '
		. 'detailed information about them includes the name of the listing brokers. Information deemed reliable but not guaranteed.'
		. "<br>\nCopyright " . date('Y') . ' REALTOR&reg; Association of the Sioux Empire, Inc., Inc. Multiple Listing Service. All rights reserved.';
	$_COMPLIANCE['disclaimer'][] .= '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Realtor Association of the Sioux Empire Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0" />';

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rase.jpg';
$_COMPLIANCE['logo_width'] = 35;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

if (!in_array($_GET['load_page'], array('map', 'directions'))) {
	        // Listing Details, Display Office Name
			        $_COMPLIANCE['details']['show_office'] = true;
}

