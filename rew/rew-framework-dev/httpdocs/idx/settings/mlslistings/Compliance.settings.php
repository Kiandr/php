<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><a href="http://www.mlslistings.com/" target="_blank"><img alt="MLSListings Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ble.png" border="0" style="float:left; margin: -2px 10px 0px -2px;" /></a> Based on information from MLSListings MLS as of <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>. <br />';
	$_COMPLIANCE['disclaimer'][] = 'All data, including all measurements and calculations of area, is obtained from various sources and has not been, and will not be, verified by broker or MLS. <br />';
	$_COMPLIANCE['disclaimer'][] = 'All information should be independently reviewed and verified for accuracy. Properties may or may not be listed by the office/agent presenting the information. </p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="MLSListings Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ble_30.png" border="0" >';

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ble_30.jpg';
$_COMPLIANCE['logo_width'] = 10; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// Dashboard, Display Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
