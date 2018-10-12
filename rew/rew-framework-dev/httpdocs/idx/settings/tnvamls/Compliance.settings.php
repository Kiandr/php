<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' Tennessee'
		. ' Virginia Regional ' . Lang::write('MLS') . '.  All rights reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information herein'
		. ' deemed reliable but not guaranteed <?=date(\'F jS, Y \a\t g:ia\', strtotime($last_updated)); ?></p>';
}

if ($_GET['load_page'] == 'search') {
	// Allow display of multiple disclaimers
	$_COMPLIANCE['multi_disclaimer'] = true;
}

// Popups, Show Disclaimer
$_COMPLIANCE['popup']['show_disclaimer'] = true;

// Dashboard, Show Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of';
$_COMPLIANCE['details']['show_office'] = true;
