<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange Program of RealTracs Solutions. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Internet Data Exchange Program logo or thumbnail logo and detailed information about them includes the name of the listing brokers.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Disclaimer: All information is believed to be accurate but not guaranteed and should be independently verified. All properties are subject to prior sale, change or withdrawal.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Realtracs Solutions Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/realtracs.png" border="0" style="float: right;" /> Copyright ' . date('Y') . ' RealTracs Solutions.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img width="80" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/realtracs.png" border="0" />';
$_COMPLIANCE['results']['show_icon@2x'] = '<img alt="Realtracs Solutions Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/realtracs-ios.png" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'birdseye', 'streetview', 'local'));

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/realtracs.png'; // Path
$_COMPLIANCE['logo_width'] = 24; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
