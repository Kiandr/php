<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<img alt="Greater Tyler Association of Realtors Logo" src="'
		. Settings::getInstance()->SETTINGS['URL_IMG']
		. 'logos/gtartx.gif" border="0" />'
		.'<p class="disclaimer">The data relating'
		.' to real estate for sale on this website comes in part from the'
		.' Internet Data Exchange (IDX) of the Greater Tyler Association'
		.' of REALTORS Multiple Listing Service. The IDX logo indicates'
		.' listings of other real estate firms that are identified in the'
		.' detailed listing information. The information being provided is'
		.' for consumers\' personal, non-commercial use and may not be used'
		.' for any purpose other than to identify prospective properties'
		.' consumers may be interested in purchasing. This information is'
		.' deemed reliable, but not guaranteed.</p>';

}

//Listing Agent and Office on details and print pages
if (in_array($_GET['load_page'], array('details','brochure'))) {
	$_COMPLIANCE['details']['show_agent'] = true;
	$_COMPLIANCE['details']['show_office'] = true;
}

$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gtartx.gif';
$_COMPLIANCE['logo_width'] = 17; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1);
