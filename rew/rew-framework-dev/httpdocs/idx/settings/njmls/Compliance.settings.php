<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute"><img alt="New Jersey MLS Logo" src="'
		. Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/njmls.jpg"'
		.' alt="NJMLS Logo" border="0" style="float: left; height: 35px; margin-right: 2px; " height="35px" />'
		.'The data relating to the real estate for sale on this web site comes in part'
		.' from the Internet Data Exchange Program of the NJMLS. Real estate listings'
		.' held by brokerage firms other than '.$broker_name
		.' are marked with the Internet Data Exchange logo and information about them'
		.' includes the name of the listing brokers. Some properties listed with the'
		.' participating brokers do not appear on this website at the request of the'
		.' seller. Listings of brokers that do not participate in Internet Data'
		.' Exchange do not appear on this website.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">All information deemed reliable'
		.' but not guaranteed. Last date updated: <?=date(\'m/d/Y\', strtotime($last_updated));'
		.' ?>. Source: New Jersey Multiple Listing Service, Inc.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">"&copy;<?=date(\'Y\');?> New Jersey Multiple'
		.' Listing Service, Inc. All rights reserved."</p>';
}

if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['show_icon'] = '<img alt="New Jersey MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
	. 'logos/njmls.jpg" border="0" style="height: 25px;" />';

$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/njmls.jpg';
$_COMPLIANCE['logo_width'] = 35;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
