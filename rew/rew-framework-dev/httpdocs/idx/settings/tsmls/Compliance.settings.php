<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Results Page Limit
$_REQUEST['page_limit'] = 9;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute"><img alt="Tahoe Sierra MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 8px 15px 5px 0;" /> The data relating to real estate for sale on this web site comes from the Broker Reciprocity Program of the Tahoe Sierra Multiple Listing Service. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Broker Reciprocity logo and detailed information about them includes the name of the listing brokers. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">The information being provided is for the consumers personal, non-commercial use and may not be used for any other purpose other than to identify prospective properties consumers may be interested in purchasing. Information deemed reliable, but not guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">Copyright ' . date('Y') . ', Tahoe Sierra Multiple Listing Service. All Rights Reserved. </p>';


}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Tahoe Sierra MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0" />';
// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map','streetview','birdseye','local'));

// Listing Details, Provide Pre-text
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of';

$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';
$_COMPLIANCE['logo_width'] = 25;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
