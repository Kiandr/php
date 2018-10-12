<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '<p>&copy; '.date('Y').' Daytona Beach Area Association of REALTORS&reg; </p>';
	$_COMPLIANCE['disclaimer'][] = '<p>Information Courtesy of Daytona Beach Area Association of REALTORS&reg; Multiple Listing Service </p>';
	$_COMPLIANCE['disclaimer'][] = "<p>IDX information is provided exclusively for consumers' personal, non-commercial use, it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing, and data is deemed reliable but is not guaranteed accurate by the MLS.</p>";
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure', 'streetview', 'birdseye'));
