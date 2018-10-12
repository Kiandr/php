<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing information provided by Rio Grande Valley MLS.';
	$_COMPLIANCE['disclaimer'][] = ' IDX Information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose';
	$_COMPLIANCE['disclaimer'][] = ' other than to identify prospective properties consumers may be interested in purchasing.';
	$_COMPLIANCE['disclaimer'][] = ' Data is deemed reliable but not guaranteed accurate by the MLS.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details','brochure'));
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details','brochure'));
