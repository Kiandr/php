<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute"><img alt="Lethbridge img src District Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ldar.jpg" alt="LDAR Logo" border="0" style="float: left; height: 35px; width: 95px; margin-right: 2px; " />NOTE: The data relating to real estate for sale on this web site comes in part from the Internet Data exchange ("IDX") program of the Lethbridge and District Association of REALTORS&reg;.'
		. ' IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.'
		. ' Copyright&copy; <?=date(\'Y\');?> Lethbridge and District Association of REALTORS&reg;. All Rights Reserved.</p>';

}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Lethbridge img src District Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ldar.jpg" border="0" style="float: left; height: 35px; width: 35px;" />';
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['details']['show_office'] = true;

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ldar.jpg';
$_COMPLIANCE['logo_width'] = 35;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
