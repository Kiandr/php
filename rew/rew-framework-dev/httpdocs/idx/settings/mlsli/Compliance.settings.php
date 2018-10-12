<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only show on certain pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'dashboard'))) {

	// MLS Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Multiple Listing Service of Long Island Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mlsli.png" border="0" style="float: left; margin: 10px; width: 100px;" />The source of the displayed data is either the property owner or public record provided by non-governmental third parties. It is believed to be reliable but not guaranteed.  This information is provided exclusively for consumers\' personal, non-commercial use. </p>';

	// Only show on certain pages
	if (in_array($_GET['load_page'], array('details', 'search', 'search_map'))) {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes in part from the Broker Reciprocity Program of the Multiple Listing Service of Long Island, Inc. </p>';
	}

	// More disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information Copyright ' . date('Y') . ', Multiple Listing Service of Long Island, Inc. All Rights Reserved. </p>';

}

$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Multiple Listing Service of Long Island Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mlsli.png" border="0" width="75" style="width: 75px;" />';


// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Information Provided Courtesy of:';

// Brochure Logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mlsli.png';
$_COMPLIANCE['logo_width'] = 15;        // Logo Width (FPDF)
$_COMPLIANCE['logo_location'] = 1;      // Logo Placement

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
