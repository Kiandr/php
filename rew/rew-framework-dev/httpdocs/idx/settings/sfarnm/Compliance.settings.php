<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Santa Fe Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/sfarnm.png" border="0" > ';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale in this Website comes in part from the Internet Data Exchange ("IDX") program of Santa Fe Association of REALTORS&reg;, Inc. from a copyrighted compilation of listings. Real estate listings held by brokers other than ' . $broker_name . ' are marked with the IDX Logo. All data in this Website is deemed reliable but is not guaranteed accurate, and should be independently verified. The information provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. All properties are subject to prior sale or withdrawal. All Rights Reserved.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Santa Fe Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/metrolist-co-thumb.png" border="0">';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Show provider first in details section
$_COMPLIANCE['details']['details_provider_first'] = true;

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/sfarnm.png';	// Path
$_COMPLIANCE['logo_width'] = 30; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
