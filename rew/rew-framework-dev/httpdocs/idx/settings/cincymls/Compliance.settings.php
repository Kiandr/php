<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<div>';
	$_COMPLIANCE['disclaimer'][] = '<p>';
	$_COMPLIANCE['disclaimer'][] = '<img alt="MLS of Greater Cincinnati Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cincy-logo.png" width="30" border="0" style="float: left; margin: 0px 10px 5px 0px;" />';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the Broker Reciprocity&trade; program of the Multiple Listing Service of Greater Cincinnati. Real estate listings held by brokerage firms other than [insert your firm’s name here] are marked with  the Broker Reciprocity&trade; logo (the small house as shown to the left) and detailed information about them includes the name of the listing brokers.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p>Information Deemed Reliable but Not Guaranteed.</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';
}

if (in_array($_GET['load_page'], array('details', 'map', 'birdseye', 'streetview', 'directions', 'local', 'brochure'))) {

	$_COMPLIANCE['disclaimer'][] = '<div>';
	$_COMPLIANCE['disclaimer'][] = '<p>Courtesy of the MLS of Greater Cincinnati.<img alt="MLS of Greater Cincinnati Logo" src="<?=Settings::getInstance()->SETTINGS[\'URL_IMG\']?>logos/cincy-logo.png" width="30" border="0" style="float: left; margin: 0px;" /></p>';
	$_COMPLIANCE['disclaimer'][] = '<p>Information Deemed Reliable but Not Guaranteed.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p>Copyright ' . date('Y') . ', MLS of Greater Cincinnati, Inc. All rights reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';
}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="MLS of Greater Cincinnati Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cincy-logo.png" border="0" width="23" style="float: right;" />';


// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['lang']['provider'] = 'Courtesy of ';
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

$_COMPLIANCE['details']['above_inquire'] = true;

// Brochure Compliance
$_COMPLIANCE['logo'][] = array(
	'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cincy-logo.png',
	'width' => 10, // Width (FPDF, not actual)
	'location' => 1, // Paragraph key
	'shift_paragraphs' => array(1, 2, 3),
	'align' => 'right'
);
