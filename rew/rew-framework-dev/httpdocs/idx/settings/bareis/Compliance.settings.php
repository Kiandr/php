<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	if (in_array($_GET['load_page'], array('details','brochure'))) {
		// Listing Details, Display Agent and Office Name
		$_COMPLIANCE['disclaimer'][] = '<p class="provider">Listing courtesy of <?=$listing[\'ListingAgent\'];?> of <?=$listing[\'ListingOffice\'];?></p>' . PHP_EOL . PHP_EOL;
	}
	$_COMPLIANCE['disclaimer'][] = '<p>Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?> and updating occurs quarter-hourly</p>';
	$_COMPLIANCE['disclaimer'][] = '<p>The information being provided by Bay Area Real Estate Information Services&reg; is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
	$_COMPLIANCE['disclaimer'][] = 'Copyright 2016, Bay Area Real Estate Information Services, Inc. All Right Reserved.</div>';

}

// Dashboard, Display MLS Mumber
$_COMPLIANCE['dashboard']['show_mls'] = true;

// Streetview, Display MLS Mumber
$_COMPLIANCE['streetview']['show_mls'] = true;

// Birdseye, Display MLS Mumber
$_COMPLIANCE['birdseye']['show_mls'] = true;

// Search Results, Display MLS Number
$_COMPLIANCE['results']['show_mls'] = true;
