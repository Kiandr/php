<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<div style="border: 2px solid black;">';

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '    <img style="float: left;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/Realtor_logo.jpg" border="0" width="35" height="35">';
	$_COMPLIANCE['disclaimer'][] = '    <img alt="Vail Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rdx.jpg" border="0" width="95" height="35">';
	$_COMPLIANCE['disclaimer'][] = '    <img style="float: right;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/equal_house.jpg" border="0" width="35" height="35">';
	$_COMPLIANCE['disclaimer'][] = '</div>';

	$_COMPLIANCE['disclaimer'][] = '<br />';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this website comes in part from the Resort Data Exchange program of the Vail Multi List Service Inc. ';
	$_COMPLIANCE['disclaimer'][] = 'and Mountain Region Multi List Service. Real estate listings belonging to brokerage firms other than ' . $broker_name . ' are marked with the RDX logo or icon. ';
	$_COMPLIANCE['disclaimer'][] = 'The detailed information about them includes the listing brokers name. ' . PHP_EOL;
	$_COMPLIANCE['disclaimer'][] = '</p>';

	$_COMPLIANCE['disclaimer'][] = '</div>';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">' . PHP_EOL;
	$_COMPLIANCE['disclaimer'][] = 'The information being provided by the Vail Board of REALTORS&reg; is exclusively for consumers\' personal, non-commercial use, and it may not be used for any';
	$_COMPLIANCE['disclaimer'][] = 'purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate ';
	$_COMPLIANCE['disclaimer'][] = 'by the MLS.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

if (in_array($_GET['load_page'], array('search', 'search_map'))) {
	$_COMPLIANCE['disclaimer'][] = '<p>';
	$_COMPLIANCE['disclaimer'][] = 'Copyright &copy; ' . date('Y') . ' Vail Multi List, Inc. (VMLS). The information displayed herein was derived from sources believed to be accurate, but has not been ';
	$_COMPLIANCE['disclaimer'][] = 'verified by VMLS. Buyers are cautioned to verify all information to their own satisfaction. This information is exclusively for viewers\' personal, ';
	$_COMPLIANCE['disclaimer'][] = 'non-commercial use. Any republication or reproduction of the information herein without the express permission of the VMLS is strictly prohibited. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Vail Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rdx.jpg" border="0" width="95" height="35">';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
$_COMPLIANCE['details']['lang']['provider'] = 'This listing courtesy of:';

// Images for Print Page (Brochure)
$_COMPLIANCE['disclaimer_print_icon_realtor'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/Realtor_logo.jpg';
$_COMPLIANCE['disclaimer_print_icon_rdx'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rdx.jpg';
$_COMPLIANCE['disclaimer_print_icon_equal_house'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/equal_house.jpg';

if (in_array($_GET['load_page'], array('brochure'))) {
	// Make some space for the logos!
	array_unshift($_COMPLIANCE['disclaimer'], '<p>&nbsp;</p>');
	array_unshift($_COMPLIANCE['disclaimer'], '<p>&nbsp;</p>');
}

// Brochure Compliance
$_COMPLIANCE['logo'] = array(
	array(
		'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/Realtor_logo.jpg',
		'width' => 10, // Width (FPDF, not actual)
		'location' => 1, // Paragraph key
		'align' => 'left', // Left or right
		'shift_paragraphs' => array(1, 2)
	),
	array(
		'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rdx.jpg',
		'width' => 10, // Width (FPDF, not actual)
		'location' => 1, // Paragraph key
		'align' => 'right', // Left or right
	),
	array(
		'logo' => Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/equal_house.jpg',
		'width' => 10, // Width (FPDF, not actual)
		'location' => 1, // Paragraph key
		'align' => 'center', // Left or right
	)
);
