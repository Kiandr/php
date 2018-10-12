<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy; ' . date('Y') . ' Triangle MLS, Inc. of North Carolina. All rights reserved. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Triangle MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tmls.gif" border="0" style="float:right; margin-left:5px;" />The data relating to real estate for sale on this web site comes in part from the Internet Data ExchangeTM Program of the Triangle MLS, Inc. of Cary. Real estate listings held by brokerage firms other than <strong><em>' . $broker_name  . '</strong></em> are marked with the Internet Data Exchange TM logo or the Internet Data ExchangeTM thumbnail logo (the TMLS logo) and detailed information about them includes the name of the listing firms. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listings marked with an icon are provided courtesy of the Triangle MLS, Inc. of North Carolina, Internet Data Exchange Database. Listing firm has attempted to offer accurate data, but the Information is Not Guaranteed and buyers are advised to confirm all items. </p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Triangle MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tmls_small.gif" style="max-width: 83px;" border="0" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Results, Display Disclaimer Immediately After Listing Results
$_COMPLIANCE['results']['show_immediately_below_listings'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;

// Listing Details, Display Disclaimer Above Inquire Form
$_COMPLIANCE['details']['above_inquire'] = true;

// Dashboard, Display MLS Number
$_COMPLIANCE['dashboard']['show_mls'] = true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/tmls.gif';
$_COMPLIANCE['logo_width'] = 40; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
