<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being'
		. ' provided by the Manhattan MLS is exclusively for consumers\' personal,'
		. ' non-commercial use, and it may not be used for any purpose other than'
		. ' to identify prospective properties consumers may be interested in'
		. ' purchasing. The data is deemed reliable but is not guaranteed accurate'
		. ' by the MLS.</p>';

}

// Search Results, Display Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Manhattan MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mmls_mls_blue.gif" border="0" width="70" height="30" />';

// Listing Details, Display Agent
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure, Display Icon
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mmls_mls_blue.gif';
$_COMPLIANCE['logo_width'] = 25;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
