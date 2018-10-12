<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

$reg_enabled = Settings::getInstance()->SETTINGS['registration'];
$reg_required = Settings::getInstance()->SETTINGS['registration_required'];

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');
if (in_array($_GET['load_page'], array('details', 'brochure')) ||
		($reg_enabled && $reg_required && in_array($_GET['load_page'], array('', 'search', 'search_map', 'map', 'streetview', 'birdseye', 'directions', 'local', 'sitemap')))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The information being provided by the Coastal Association of REALTORS&reg; is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.';
	$_COMPLIANCE['disclaimer'][] = 'The data is deemed reliable but is not guaranteed accurate by the MLS.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

// Show office on results
$_COMPLIANCE['results']['show_office'] = !empty($reg_enabled) && in_array($_GET['load_page'], array('sitemap'));

$_COMPLIANCE['details']['show_agent'] = true;
$_COMPLIANCE['details']['show_office'] = true;
