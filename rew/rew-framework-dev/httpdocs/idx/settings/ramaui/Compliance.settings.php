<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listings provided courtesy of the REALTORS&reg; Association of Maui. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';

}

$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['results']['show_above_actions'] = true;
$_COMPLIANCE['details']['show_extras'] = array('DescriptionLandTenure');
$_COMPLIANCE['results']['show_extras'] = array('DescriptionLandTenure');
