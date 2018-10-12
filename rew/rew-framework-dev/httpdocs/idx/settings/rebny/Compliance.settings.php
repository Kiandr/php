<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['limit'] = 2500;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="The Real Estate Board of New York Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rebny.jpg" alt="REBNY Logo" border="0" /></p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">This information is'
		.' not verified for authenticity or accuracy and is not guaranteed'
		.' and may not reflect all real estate activity in the market.'
		.' &copy;' . date('Y') . ' REBNY Listing Service, Inc. All rights reserved.</p>';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The IDX information'
		.' is provided exclusively for consumers\' personal, non-commercial'
		.' use and it may not be used for any purpose other than to'
		.' identify prospective properties consumers may be interested in'
		.' purchasing.</p>';
}

$_COMPLIANCE['results']['show_icon'] = '<img alt="The Real Estate Board of New York Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rebny.jpg" border="0" />';

if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rebny.jpg';
$_COMPLIANCE['logo_width'] = 25;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
