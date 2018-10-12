<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Greater Northwest Indiana Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
		. 'logos/br.jpg" style="float: left; width: 95px; height: 35px; margin: 10px 15px 5px 0px;" border="0" />'
		. ' &copy;' . date("Y") . ' Multiple Listing Service of the Greater Northwest Indiana'
		. ' Association of REALTORS&reg;, Inc. All rights reserved. The data relating'
		. ' to real estate for sale on this website comes in part from the Internet'
		. ' Data Exchange Program of the Multiple Listing Service of the Greater'
		. ' Northwest Indiana Association of REALTORS&reg;, Inc. Real estate'
		. ' listings held by brokerage firms other than ' . $broker_name
		. ' are marked with the Broker Reciprocity logo or the Broker Reciprocity'
		. ' thumbnail logo (a little black house) and detailed information about'
		. ' them includes the name of the listing brokers. Information deemed'
		. ' reliable but not guaranteed by the GNIAR MLS.</p>';
}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Greater Northwest Indiana Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house-32.png" style="width: 35px; height: 35px;" border="0" />';

// Listing Brochure, Display Thumbnail Icon
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . "logos/br.jpg";

// Listing Brochure, Location of Thumbnail Icon
$_COMPLIANCE['logo_location'] = 1;

// Listing Brochure, Width of Thumbnail Icon
$_COMPLIANCE['logo_width'] = 16;
