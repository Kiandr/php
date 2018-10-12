<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Greater Baton Rouge Association of Realtors Logo" src="'
		. Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gbrar_logo.jpg"'
		.' border="0" width="100px"style="float: left; margin: 10px; width: 100px;" />The'
		.' information being provided by the Greater Baton Rouge Association'
		.' of REALTORS&reg; is exclusively for consumers\' personal, non-commercial'
		.' use, and it may not be used for any purpose other than to identify'
		.' prospective properties consumers may be interested in purchasing.'
		.' The data is deemed reliable but is not guaranteed accurate by the MLS.'
		.'</p>';
}

// Results icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Greater Baton Rouge Association of Realtors Logo" src="'
	. Settings::getInstance()->SETTINGS['URL_IMG']
	. 'logos/gbrar_logo_small.png" border="0" />';

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG']
	. 'logos/gbrar_logo_small.png';
$_COMPLIANCE['logo_width'] = 20;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
