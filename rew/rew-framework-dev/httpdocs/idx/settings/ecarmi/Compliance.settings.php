<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Only 250 listings can be displayed per search
$_COMPLIANCE['limit'] = 250;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . 'Properties marked with the ECAR IDX logo are provided courtesy of East Central Association of REALTORS&reg;. '
        . 'Listing information is provided exclusively for consumers\' personal, non-commercial use and may '
        . 'not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Data is deemed reliable but not guaranteed accurate by the '
        . 'MLS. The accuracy of all information, regardless of source, is not guaranteed or warranted. All information should be independently verified.'
        . '</p>';

} else if (in_array($_GET['load_page'], array('details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . '<img alt="East Central Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ecarmi.png" />'
        . 'Provided through IDX through East Central Association of REALTORS&reg;. Courtesy of <?=$listing[\'ListingOffice\']; ?>'
        . '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . 'Copyright 2016 East Central Association of REALTORS&reg; Listing information is provided exclusively for consumers\' personal, non-commercial use and may '
        . 'not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Data is deemed reliable but not guaranteed accurate by the '
        . 'MLS. The accuracy of all information, regardless of source, is not guaranteed or warranted. All information should be independently verified.'
        . '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="East Central Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ecarmi.png" border="0" />';

// Show logo on brochure
if (in_array($_GET['load_page'], array('brochure'))) {
	$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ecarmi.png';
	$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
	$_COMPLIANCE['logo_location'] = 1; // Paragraph key
}
