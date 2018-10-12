<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = "The IDX Data is provided by the Spokane Association of REALTORS  is exclusively for consumers' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the SARMLS.";
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = "Copyright ".date('Y')." of the Spokane Association of REALTORS&reg; MLS. All rights reserved.";
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'Date Last Updated: <?=date(\'Y/m/d\', strtotime($last_updated)); ?>';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

$_COMPLIANCE['results']['show_office'] = true;

if (!in_array($_GET['load_page'], array('map','local'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}
