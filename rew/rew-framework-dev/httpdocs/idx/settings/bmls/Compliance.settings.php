<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer']   = array('');

// Only Show On Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer Text
	$_COMPLIANCE['disclaimer'][] = '<div class="disclaimer-rfmls-fl">';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">';
	$_COMPLIANCE['disclaimer'][] = '<img width="160" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/BMLS-large.jpg" style="width: 160px; margin: 0px 10px 10px 0px; float: left;" />';
	$_COMPLIANCE['disclaimer'][] = 'All listings featuring the BMLS logo are provided by Beaches MLS Inc. Copyright ' . date('Y') . ' Beaches MLS. This information is not verified for authenticity or accuracy and is not guaranteed.<br />';
	$_COMPLIANCE['disclaimer'][] = '&copy; ' . date('Y') . ' Beaches Multiple Listing Service, Inc. All rights reserved. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/BMLS-large.jpg';
$_COMPLIANCE['logo_width'] = 24; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

//Logo On List View Results
$_COMPLIANCE['results']['show_icon'] = '<img alt="Beaches Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/BMLS-large.jpg" border="0" width="160px" />';
