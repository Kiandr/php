<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing information is provided by Iowa City Area Association of REALTORS&reg;. </p>';

}

$_COMPLIANCE['details']['show_agent'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));
