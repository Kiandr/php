<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="West Central Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" /> The real estate listing information and related content displayed on this site is provided exclusively for consumers\' personal, non-commercial use and, may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. This information and related content is deemed reliable but is not guaranteed accurate by West Central Association of Realtors&reg;. </p>';

}
// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

?>
