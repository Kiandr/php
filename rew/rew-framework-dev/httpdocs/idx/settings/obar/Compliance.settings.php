<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this Web Site comes in part from the IDX Program of the Outer Banks Association of REALTORS&reg; Multiple Listing Service. &copy; Copyright ' . date('Y') . '.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Outer Banks Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/obar.jpg" border="0" style="float: right; margin: 5px;" />';
	$_COMPLIANCE['disclaimer'][] = 'The MLS IDX information on this web site is being provided to you by the Outer Banks Association of REALTORS&reg; for general informational purposes only. Contact your real estate broker or sales person for the most up to date information regarding these listings. Real estate listings include the name of the brokerage firms and listing agents. THE OUTER BANKS ASSOCIATION OF REALTORS&reg; AND ' . strtoupper($broker_name) . ' DOES NOT GUARANTEE THE ACCURACY OF THIS MLS IDX INFORMATION DISPLAYED ON THESE WEBSITE PAGES. THE OUTER BANKS ASSOCIATION OF REALTORS&reg; AND ' . strtoupper($broker_name) . ' CAN NOT BE HELD LIABLE FOR ANY DAMAGES OF ANY KIND RESULTING FROM THE INACCURACY OF THE MLS IDX INFORMATION. All MLS IDX information is owned by the Outer Banks Association of REALTORS&reg; and any repackaging or other use of this information is strictly prohibited. The Outer Banks Association of REALTORS&reg; reserves the right to terminate your access to this information at any time. By submitting this search form, and or using this websites features, you agree to these terms.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/obar.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
