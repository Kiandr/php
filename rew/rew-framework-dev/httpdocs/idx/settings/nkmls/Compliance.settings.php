<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Broker name
$broker_name = '[INSERT BROKER NAME]';

// Details disclaimer needs larger font
$style = in_array($_GET['load_page'], array('details')) ? ' style="font-size: 16px;"' : '';

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

// Build disclaimer
$_COMPLIANCE['disclaimer'][] = '<p' . $style . 'class="disclaimer">';
if (in_array($_GET['load_page'], array('details'))) {
	$_COMPLIANCE['disclaimer'][] = '<img width="130" src="' .Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-large.png" style="float: left; margin-right: 12px; width: 130px;">';
}
$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange Program of NKMLS. ';
$_COMPLIANCE['disclaimer'][] = 'Real estate listings held by IDX Brokerage firms other than ' . $broker_name . ' are marked with the Internet Data Exchange logo or the Internet Data Exchange thumbnail logo and detailed information about them includes the name of the listing Brokers. ';
$_COMPLIANCE['disclaimer'][] = '</p>';
$_COMPLIANCE['disclaimer'][] = '<p' . $style . ' class="disclaimer">Information Deemed Reliable but Not Guaranteed.</p>';

if (in_array($_GET['load_page'], array('details','brochure'))) {
	$_COMPLIANCE['disclaimer'][] = '<p' . $style . ' class="disclaimer">Copyright ' .date('Y') . ' NKMLS. All rights reserved. </p>';
}

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = false;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = false;

$_COMPLIANCE['results']['show_icon'] = '<img width="100" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-large.png" style="width: 100px;">';

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = false;

// Listing Details, Display Office Name
if(!in_array($_GET['load_page'], array('map','birdseye','local','streetview'))){
    $_COMPLIANCE['details']['show_office'] = true;
}

// Provider prefix
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of';

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-large.png';
$_COMPLIANCE['logo_width'] = 35; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['brochure']['disclaimer_size'] = 13;
$_COMPLIANCE['brochure']['disclaimer_height'] = 5;
