<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 50;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">' . '<img alt="Central Alberta Realtors Association Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cara.png" border="0" style="float: right; margin: 0 0 0.5em 15px;" />'
		. 'The data included in this display is deemed reliable, but is not guaranteed to be accurate by the Central Alberta REALTORS&reg; Association.'
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The real estate listing information and related content displayed on this site is provided exclusively for consumers. personal, non-commercial use and, may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. This information and related content is deemed reliable but is not guaranteed accurate. '
		. '</p>';
}

// Search Results: Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = $_GET['load_page'] != 'map';

// Brochure Logo
$_COMPLIANCE['logo'][] = array(
	'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cara.png',
	'width' => 35, // Width (FPDF)
	'location' => 2, // Paragraph key
	'align' => 'right', // Paragraph key
);
