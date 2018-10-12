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
	$_COMPLIANCE['disclaimer'][] = 'The information being provided by the Sullivan County Board Of REALTORS® is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. <br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'The data is deemed reliable but is not guaranteed accurate by the MLS. <br /><br />';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

//Hide Office on detailed view when lead signed in
$_COMPLIANCE['hide_office_grid'] = User_Session::get()->isValid();

//Hide Office on map when lead signed in
$_COMPLIANCE['hide_office_map'] = User_Session::get()->isValid();

//Display office on details
$_COMPLIANCE['details']['show_office'] = true;
