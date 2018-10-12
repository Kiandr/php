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
	$_COMPLIANCE['disclaimer'][] = '<img alt="Alaska Multiple Listing Service Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/IDX_Alaska_MLS_Logo_Large.png" border="0" style="float: left; margin: 0 15px 0.5em 0;"> ';
	$_COMPLIANCE['disclaimer'][] = 'DISCLAIMER: The listing content relating to real estate for sale on this web site comes in part from the AK MLS IDX of Alaska Multiple Listing Service, Inc. (AK MLS). Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with either the listing brokerage\'s logo or the AK MLS IDX logo and information about them includes the name of the listing brokerage. <br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'All information is deemed reliable but is not guaranteed and should be independently verified for accuracy.<br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'Listing information is updated every fifteen minutes.<br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'Copyright ' . date('Y') . ' Alaska Multiple Listing Service, Inc. All rights reserved.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Alaska Multiple Listing Service Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/IDX_Alaska_MLS_Logo_Small.png" border="0">';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Search Results, Display Provider Above View Listing Button
$_COMPLIANCE['results']['show_above_actions'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/IDX_Alaska_MLS_Logo_Large.png';	// Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key

$_COMPLIANCE['results']['lang']['provider'] = 'Listing Office: ';
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Office: ';
