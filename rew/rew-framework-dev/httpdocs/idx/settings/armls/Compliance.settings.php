<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Arizona Regional Multiple Listings Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/armls.jpg" height="40" style="height: 40px; width: auto; float: left;" border="0" /> Information Deemed Reliable But Not Guaranteed. All information should be verified by the recipient and none is guaranteed as accurate by ARMLS. ARMLS Logo indicates that a property listed by a real estate brokerage other than ' . $broker_name . '. Copyright ' . date('Y') . ' Arizona Regional Multiple Listing Service, Inc. All rights reserved.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Arizona Regional Multiple Listings Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/armls-thumb.gif" border="0" />';

// Listing Details, Display Office Name
if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/armls.jpg'; // Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}