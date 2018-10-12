<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Mid America Regional Info Systems Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/maris.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" /> Information from Third Parties, Deemed Reliable but Not Verified.<br />';
	$_COMPLIANCE['disclaimer'][] = 'Listings displaying the MARIS logo are courtesy of Mid America Regional Information Systems Internet Data Exchange.</p>';
}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Mid America Regional Info Systems Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/maris-small.jpg" border="0" />';

$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/maris.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
