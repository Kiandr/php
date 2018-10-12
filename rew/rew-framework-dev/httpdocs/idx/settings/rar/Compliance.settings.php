<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Roswell Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;"> ';
	$_COMPLIANCE['disclaimer'][] = '&copy; ' . date('Y') . ' Roswell Board of REALTORS The data relating to real estate for sale in this web site come in part from the ';
	$_COMPLIANCE['disclaimer'][] = 'Internet Data Exchange (\'IDX\') program of the Roswell Association of REALTORS &reg; MLS. Real Estate listings held by brokers other than ' . $broker_name . ' are marked with the IDX Logo. ';
	$_COMPLIANCE['disclaimer'][] = 'All data in this web site is deemed reliable but is not guaranteed. Last Updated on ' .  date('Y-m-j\.');
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Roswell Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0">';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';	// Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key

$_COMPLIANCE['details']['lang']['provider'] = 'Courtesy of ';
