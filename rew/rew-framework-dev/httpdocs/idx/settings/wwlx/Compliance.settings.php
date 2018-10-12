<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real'
		.' estate on this web site comes in part from the Internet Data Exchange'
		.' Program of the Water Wonderland MLS (WWLX) (989-732-8226) .  Real estate'
		.' listings held by brokerage firms other than ' . $broker_name
		.' are marked with the WWLX logo and the detailed information about said'
		.' listing includes the listing office.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information deemed'
		.' reliable but not guaranteed and should be independently verified.'
		.' All properties are subject to prior sale, change or withdrawal. Neither'
		.' the listing broker(s) nor ' . $broker_name
		.' shall be responsible for any typographical errors, misinformation,'
		.' misprints, and shall be held totally harmless. Water Wonderland MLS, Inc'
		.' &copy; All rights reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">WWLX information is'
		.' provided exclusively for consumers\' personal, non-commercial use'
		.' and may not be used for any purpose other than to identify prospective'
		.' properties consumers may be interested in purchasing.</p>';

}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Water Wonderland Board of Realtors Logo" src="'
	. Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/wwlxlogo.bmp" border="0" />';
