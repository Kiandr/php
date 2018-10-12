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
	$_COMPLIANCE['disclaimer'][] = '<div class="disclaimer-jths">';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Jupiter-Tequesta-Hobe Sound Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/JTHS-large.jpg" style="margin: 0px 10px 10px 0px; float: left;" />';
	$_COMPLIANCE['disclaimer'][] = 'All listings featuring the JTHS MLS logo are provided by Jupiter Tequesta Hobe Sound MLS Inc. Copyright ' . date('Y') . ' Jupiter Tequesta Hobe Sound MLS. This information is not verified for authenticity or accuracy and is not guaranteed.<br />';
	$_COMPLIANCE['disclaimer'][] = '&copy; ' . date('Y') . ' Jupiter Tequesta Hobe Sound Multiple Listing Service, Inc. All rights reserved. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Jupiter-Tequesta-Hobe Sound Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/JTHS-small.jpg" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/JTHS-small.jpg';
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}