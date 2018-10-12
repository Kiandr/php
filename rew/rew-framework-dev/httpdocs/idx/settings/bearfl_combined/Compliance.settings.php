<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Bonita Springs-Estero Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
		. 'logos/brlogo95.jpg" style="float: left; margin: 10px 15px 5px 0px;" border="0" />'
		. ' The data relating to real estate for sale on this Website come in part'
		. ' from the Broker Reciprocity Program (BR Program) of MLS of Bonita'
		. ' Springs-Estero Association of REALTORS&reg;. Properties listed with'
		. ' brokerage firms other than ' . $broker_name . ' are marked with the'
		. ' BR Program Icon or the BR House Icon and detailed information about'
		. ' them includes the name of the Listing Brokers. The properties'
		. ' displayed may not be all the properties available through the BR'
		. ' Program.</p>';
}

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Append an additional disclaimer on the details and brochure pages
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The source of this real'
		. ' property information is the copyrighted and proprietary database'
		. ' compilation of the Bonita Springs-Estero Association of REALTORS&reg;.'
		. ' Copyright ' . date('Y') . ' MLS of Bonita Springs-Estero Association of'
		. ' REALTORS&reg;. All rights reserved. The accuracy of this information'
		. ' is not warranted or guaranteed. This information should be'
		. ' independently verified if any person intends to engage in a'
		. ' transaction in reliance upon it.</p>';
}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Bonita Springs-Estero Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" style="margin-top: -15px;" border="0" />';

// Listing Brochure, Display Thumbnail Icon
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . "logos/brlogo95.jpg";

// Listing Brochure, Location of Thumbnail Icon
$_COMPLIANCE['logo_location'] = 1;

// Listing Brochure, Width of Thumbnail Icon
$_COMPLIANCE['logo_width'] = 16;
