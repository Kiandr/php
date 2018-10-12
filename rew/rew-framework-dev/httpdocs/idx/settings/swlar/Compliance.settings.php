<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The information being provided by the Southwest Louisiana Association of REALTORS&reg;. ';
	$_COMPLIANCE['disclaimer'][] = 'The information is provided exclusively for consumers\' personal, non-commercial use, ';
	$_COMPLIANCE['disclaimer'][] = 'and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. ';
	$_COMPLIANCE['disclaimer'][] = 'The data is deemed reliable but is not guaranteed accurate by the MLS. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

$_COMPLIANCE['details']['lang']['provider'] = 'Listing Office: ';
