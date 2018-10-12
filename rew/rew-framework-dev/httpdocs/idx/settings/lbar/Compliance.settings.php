<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	if (in_array($_GET['load_page'], array('search'))) {
		$_COMPLIANCE['disclaimer'][] = '<p>Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?> and updating occurs every 15 minutes</p>';
	}
	if (in_array($_GET['load_page'], array('details','brochure'))) {
		$_COMPLIANCE['disclaimer'][] = '<p>Listing Provided Courtesy of: <?=$listing["ListingOffice"]; ?></p>';
	}
	$_COMPLIANCE['disclaimer'][] = '<p>';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Lexington-Bluegrass Assocation of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/lbar_idx.jpg" alt="IDX Logo" border="0" />';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange Program of the Lexington-Bluegrass Association of Realtors Multiple Listing Service. Real estate listings held by IDX Brokerage firms other than ' . $broker_name . ' are marked with the IDX logo or the IDX thumbnail logo and detailed information about them includes the name of the listing IDX Brokers.';
	$_COMPLIANCE['disclaimer'][] = 'Information Deemed Reliable but Not Guaranteed';
	$_COMPLIANCE['disclaimer'][] = '&copy;'.date('Y').' LBAR Multiple Listing Service. All rights reserved.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

$_COMPLIANCE['details']['above_inquire'] = true;

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Lexington-Bluegrass Assocation of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/lbar_idx.jpg" border="0" />';

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/lbar_idx.jpg';
$_COMPLIANCE['logo_width'] = 8; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
