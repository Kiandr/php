<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">This multiple'
		.' listing information is provided by the Amarillo Association'
		.' of Realtors&reg; from a confidential and copyrights compilation'
		.' of listings. The compilation of listings and each individual'
		.' listing are &copy <?=date(\'Y\', strtotime($last_updated)); ?>'
		.' Amarillo Association of Realtors. All Rights Reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information'
		.' provided is for consumers\' personal, non-commercial use and'
		.' may not be used for any purpose other than to identify'
		.' prospective properties consumers may be interested in purchasing.'
		.' All properties are subject to prior sale or withdrawal. All'
		.' information provided is deemed reliable but is not guaranteed'
		.' accurate, and should be independently verified.</p>';

}

// Dashboard, Show Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// The listing office & agent are required on the details & print pages
if (in_array($_GET['load_page'], array('details','brochure'))) {
	$_COMPLIANCE['details']['show_agent'] = true;
	$_COMPLIANCE['details']['show_office'] = true;
}
