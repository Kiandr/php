<?php

global $_COMPLIANCE;
$_COMPLIANCE = array();

$_COMPLIANCE['limit'] = 250;

$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p>&copy; Copyright ' . date('Y') . ' of the Coastal Carolinas Association of REALTORS&reg; MLS. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p><img alt="Coastal Carolinas Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ccar.jpg" style="float: left; margin: 10px 10px 0 0;" > IDX information is provided by the Coastal Carolinas Association of REALTORS&reg; for consumer\'s personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing, data is deemed reliable but is not guaranteed accurate by the MLS.</p>';

}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Coastal Carolinas Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ccar.jpg" border="0" style="height: 20px;"><br>';
$_COMPLIANCE['results']['show_office'] = true;

$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));

$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ccar.jpg';
$_COMPLIANCE['logo_width'] = 25; // Width (FPDF)
$_COMPLIANCE['logo_location'] = 2; // Placement
