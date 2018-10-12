<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (Settings::getInstance()->SETTINGS['registration_required'] !== false) {
	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '<p>The information being provided by the Snake River MLS is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';
}
else if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '<p>The information being provided by the Snake River MLS is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = !in_array($_GET['load_page'], array('map', 'local'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
