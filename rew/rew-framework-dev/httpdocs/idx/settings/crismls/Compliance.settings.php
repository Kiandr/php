<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="CRIS MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/normls.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" />';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this Web site comes in part from the Internet Data Exchange Program of CRIS. The information provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. All information provided is deemed reliable but not guaranteed accurate, and should be independently verified. Real estate listings held by brokerage firms other than '.$broker_name.' are marked with the Internet Data Exchange logo and detailed information about them includes the name of the listing broker(s).</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy;' . date("Y") . ' CRIS Multiple Listing Service. All Rights reserved</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="CRIS MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/normls-small.jpg" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
$_COMPLIANCE['details']['lang']['provider'] = 'Courtesy Of:';

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/normls.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
