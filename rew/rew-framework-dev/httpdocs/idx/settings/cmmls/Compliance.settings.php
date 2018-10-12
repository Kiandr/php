<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being'
		. ' provided by the Central Mississippi MLS, Inc. is exclusively for'
		. ' consumers\' personal, non-commercial use, and it may not be used'
		. ' for any purpose other than to identify prospective properties'
		. ' consumers may be interested in purchasing. The data is deemed'
		. ' reliable but is not guaranteed accurate by the MLS.</p>';

}

if (empty(Settings::getInstance()->SETTINGS['registration'])
		&& !in_array($_GET['load_page'], array('dashboard'))) {
	// Search Results, Display Agent Name
	$_COMPLIANCE['results']['show_agent'] = true;

	// Search Results, Display Office Name
	$_COMPLIANCE['results']['show_office'] = true;
}
