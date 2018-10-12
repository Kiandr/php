<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

// Disclaimer
$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'map', 'streetview', 'birdseye', 'directions', 'local', 'sitemap', 'search_form', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<img style="height: 25px; width: 50px;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/bnar-logo.png" border="0" /> ';
        $_COMPLIANCE['disclaimer'][] = 'The data relating to real estate on this web site comes in part from the Internet Data Exchange (IDX) Program of NYSAMLS’s. '
                . 'Real estate listings held by firms other than (Your Firm Name here), are marked with the IDX logo and detailed information about them includes the Listing Broker’s Firm Name. '
                . 'Date of Last update ' . date('m\/d\/Y\.');

} else if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	$_COMPLIANCE['disclaimer'][] = '<img style="height: 25px; width: 50px;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/bnar-logo.png" border="0" /> ';
	$_COMPLIANCE['disclaimer'][] = 'All information deemed reliable but not guaranteed and should be independently verified. All properties are subject to prior sale, change or withdrawal. '
		. 'Neither the listing broker(s) nor (Your Firm name here) shall be responsible for any typographical errors, misinformation, misprints, and shall be held totally harmless. '
		. '&copy; ' . date('Y') . ' CNYIS, GENRIS, WNYREIS. All rights reserved.';

	// Listing Details, Display Office Name
	$_COMPLIANCE['details']['lang']['provider'] = 'Courtesy of ';
	$_COMPLIANCE['details']['show_office'] = true;

}

$_COMPLIANCE['disclaimer'][] = '</p>';

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="New York State Alliance MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/bnar-logo.png" />';

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/bnar-logo.png';    // Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key

$_COMPLIANCE['dashboard']['show_mls'] = true;
$_COMPLIANCE['results']['show_mls'] = true;
