<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Wilmington Regional Assocation of Realtors (Commercial) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/wrar.gif" border="0" style="float: left; margin: 0 15px 0.5em 0;" />';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate on this web site comes in part from the Internet Data Exchange program of the MLS of the Wilmington Regional Association of REALTORS&reg;, and is updated as of <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?> (date/time).';
	$_COMPLIANCE['disclaimer'][] = 'All information is deemed reliable but not guaranteed and should be independently verified. All properties are subject to prior sale, change, or withdrawal. Neither listing broker(s) nor ' . $broker_name . ' shall be responsible for any typographical errors, misinformation, or misprints, and shall be held totally harmless from any damages arising from reliance upon these data. &copy; ' . date('Y') . ' MLS of WRAR, Inc.';
	$_COMPLIANCE['disclaimer'][] = '</p>';


}

// Search Results, Display MLS Logo
$_COMPLIANCE['results']['show_icon'] = '<img alt="Wilmington Regional Assocation of Realtors (Commercial) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/wrar.gif" border="0" style="height: 24px;" height="24" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));

// FPDF Brochure
$_COMPLIANCE['logo']          = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/wrar.gif';
$_COMPLIANCE['logo_width']    = 15;     // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1;      // Paragraph key
