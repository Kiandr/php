<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimers
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Ulster County Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ucmls.gif" border="0" alt="IDX Logo" style="float: left; margin: 10px;" /> The data relating to real estate on this website comes in part from the IDX of the Multiple Listing Service of Ulster County, Inc.  Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with IDX logo and the detailed information about them includes the name of the listing broker.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright &copy; ' . date('Y') . ' by the Multiple Listing Service of Ulster County, Inc. All Rights Reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information provided is deemed reliable but is not guaranteed and should be independently verified.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in  purchasing.</p>';

}

// Search Results, Display MLS Logo
$_COMPLIANCE['results']['show_icon'] = '<img alt="Ulster County Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ucmls.gif" border="0" alt="IDX Logo" style="margin-top: -5px; width: 50px;" />';

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ucmls.gif'; // Path
$_COMPLIANCE['logo_width'] = 15; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
