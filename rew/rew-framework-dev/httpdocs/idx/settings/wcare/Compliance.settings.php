<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		.'<img alt="West Central Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
		.'logos/InternetDataExchangelogo.gif" alt="WCARE Logo" border="0"'
		.' style="float: left; margin-right: 2px; " />'
		.'Copyright &copy; ' . date("Y") . ','
		.' West Central Association of REALTORS&reg;MLS. All information provided'
		.' is deemed reliable but is not guaranteed and should be independently verified.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The IDX information is'
		.' provided exclusively for consumers\' personal, non-commercial use, it'
		.' may not be used for any purpose other than to identify prospective'
		.' properties consumers may be interested in purchasing. All information'
		.' provided is deemed reliable but is not guaranteed accurate by the MLS'
		.' and should be independently verified.</p>';
}
if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_agent'] = true;
	$_COMPLIANCE['details']['show_office'] = true;
}
$_COMPLIANCE['results']['show_icon'] = '<img alt="West Central Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
	. 'logos/InternetDataExchangelogo.gif" border="0" />';

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG']
	. 'logos/InternetDataExchangelogo.gif';
$_COMPLIANCE['logo_width'] = 15;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
