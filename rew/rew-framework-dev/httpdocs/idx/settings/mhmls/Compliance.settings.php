<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

if (in_array($_GET['load_page'], array('search'))) {

	// Display Update Time
	$_COMPLIANCE['update_time'] = true;

}

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '<p>The data relating to real estate for sale or rent on this web site comes from the Mid-Hudson Multiple Listing Service, LLC. IDX information is provided exclusively for consumers\' personal, non-commercial use, that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information deemed reliable but not guaranteed and should be verified.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p>Copyright ' . date('Y') . ' Mid-Hudson Multiple Listing Service, LLC. All rights reserved.</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Mid-Hudson Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mid-hudson.png" border="0" />';

$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details', 'brochure'))) ? true : false;
$_COMPLIANCE['details']['show_office_phone'] = true;
