<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '&copy; ' . date('Y') . ' Coastal Bend Multiple Listing Service. ';
	$_COMPLIANCE['disclaimer'][] = 'IDX information is provided exclusively for consumers’ personal, non-commercial use, ';
	$_COMPLIANCE['disclaimer'][] = 'that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing, ';
	$_COMPLIANCE['disclaimer'][] = 'and that data is deemed reliable but is not guaranteed accurate by the MLS.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'results', 'brochure'));

$_COMPLIANCE['show_list_view'] = true;
