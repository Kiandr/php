<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'Copyright &copy; 2016 Gulf Coast MLS, Inc. All rights'
		. ' reserved. All GCMLS information is being provided exclusively for'
		. ' consumers\' personal, non-commercial use and may not be used for'
		. ' any purpose other than to identify prospective properties'
		. ' consumers may be interested in purchasing. The data is deemed'
		. ' reliable but is not guaranteed accurate by the MLS.'
		. '</p>';

}

// Listing Details, Display Office & Agent Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));
