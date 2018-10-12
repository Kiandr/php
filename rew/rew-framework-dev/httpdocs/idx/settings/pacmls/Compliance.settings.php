<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 150;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array();

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to'
		. ' real estate for sale on this website comes in part from the Tri Cities'
		. ' Association of REALTORS&reg;. The information being provided is for '
		. 'consumers\' personal, non-commercial use and may not be used for any '
		. 'purpose other than to identify prospective properties consumers may '
		. 'be interested in purchasing.</p>';

}

$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;
