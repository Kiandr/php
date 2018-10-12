<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing data is provided courtesy of the South Tahoe Association of REALTORS&reg; and the Multiple Listing Service, Inc. Internet Data Exchange Database. Information is deemed reliable, but not guaranteed. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date('Y') . ', South Tahoe Association of REALTORS&reg;. All Rights Reserved. </p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
