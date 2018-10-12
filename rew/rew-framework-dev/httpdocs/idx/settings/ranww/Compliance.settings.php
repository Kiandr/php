<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div align="center">';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Realtor Association of North Western Wisconsin Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ranww-logo.gif" alt="RANWW Logo" border="0" />';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright '.date('Y').' Realtors&reg; Association of Northwestern Wisconsin. All rights reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information is provided for consumer\'s personal, non-commercial use and may not be used for any purpose other than to identify prospective properties.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Data is deemed reliable but is not guaranteed accurate by the MLS.</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Realtor Association of North Western Wisconsin Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ranww-logo.gif" border="0" alt="RANWW Logo" width="50" />';
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ranww-logo.gif';
$_COMPLIANCE['logo_width'] = 12;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement

// Listing Details, Display Office Name
if (!in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'directions', 'local'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}
