<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 350;

// Results icon
$_COMPLIANCE['results']['show_icon'] = '<a href="http://www.vreb.org/vreb/VREBReciprocityProgram.html" ><img alt="Victoria Real Estate Board Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/vreb_logo.png" width="95"></a>';

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<a href="http://www.vreb.org/vreb/VREBReciprocityProgram.html" ><img alt="Victoria Real Estate Board Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/vreb_logo.png" width="95" style="float: left; margin-right: 12px;" ></a>';
	$_COMPLIANCE['disclaimer'][] = 'NOTE: ' . Lang::write('MLS') . ' property information is provided under copyright&copy; by the Victoria Real Estate Board. The information is from sources deemed reliable, but should not be relied upon without independent verification. The website must only be used by consumers for the purpose of locating and purchasing real estate.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/vreb_logo.png';
$_COMPLIANCE['logo_width'] = 25;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
if (in_array($_GET['load_page'], array('details', 'streetview', 'birdseye', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Dashboard, Display Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;
