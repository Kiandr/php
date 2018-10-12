<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Charleston Trident Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" /> The data relating to real estate for sale on this web site comes in part from the Broker Reciprocity<sup>SM</sup>  Program of the Charleston Trident Multiple Listing Service. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Broker Reciprocity<sup>SM</sup> logo or the Broker Reciprocity<sup>SM</sup> thumbnail logo (a little black house) and detailed information about them includes the name of the listing brokers.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The broker providing these data believes them to be correct, but advises interested parties to confirm them before relying on them in a purchase decision.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' Charleston Trident Multiple Listing Service, Inc. All rights reserved.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Charleston Trident Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg'; // Path
$_COMPLIANCE['logo_width'] = 16; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
