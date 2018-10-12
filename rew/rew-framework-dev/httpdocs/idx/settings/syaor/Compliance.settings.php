<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Sutter-Yuba Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/syaor.png" border="0" > ';
	$_COMPLIANCE['disclaimer'][] = 'Copyright &copy;' . date('Y') . ' SYAOR MLS.';
	$_COMPLIANCE['disclaimer'][] = ' The data relating to real estate for sale on this web site comes in part from the Sutter-Yuba Association of Realtors\' Internet Data Exchange. Real estate listings held by brokerage firms other than  ' . $broker_name . ' are marked with the IDX logo and detailed information about them includes the name of the listing brokers. <br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information is deemed reliable but is not guaranteed and should be independently verified.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Sutter-Yuba Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/syaor.png" border="0" width="25" height="17">';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Search Results, Display Provider Above View Listing Button
$_COMPLIANCE['results']['show_above_actions'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/syaor.png';	// Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key

$_COMPLIANCE['results']['lang']['provider'] = 'Courtesy of ';
$_COMPLIANCE['details']['lang']['provider'] = 'Courtesy of ';
