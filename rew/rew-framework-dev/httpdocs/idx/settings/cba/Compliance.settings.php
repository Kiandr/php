<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Commercial Brokers Association Logo" src="'
		.Settings::getInstance()->SETTINGS['URL_IMG'].'logos/CBAlogoMAIN.jpg"'
		.' border="0" style="height: 35px;" />&copy; Copyright <?=date(\'Y\');?>'
		.' Commercial Brokers Association. All rights reserved.</p>'
		.'<p class="disclaimer"> All information provided is deemed reliable'
		.' but is not guaranteed and should be independently verified.</p>';

}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Commercial Brokers Association Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
	.'logos/CBAlogoMAIN.jpg" border="0" style="height: 35px;" />';

$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/CBAlogoMAIN.jpg';
$_COMPLIANCE['logo_width'] = 15;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
