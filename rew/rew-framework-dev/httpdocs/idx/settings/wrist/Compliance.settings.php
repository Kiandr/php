<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = false;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'dashboard', 'details', 'search_map', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap'))) {

	$_COMPLIANCE['disclaimer'][] = '<p>';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Western Regional Information Systems img src Technology Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/wrist-logo.jpg" width="60px" height="20px" border="0" style="float: left; margin: 0px 10px 0px 0px;" />';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange Program of Western Regional Information Systems and Technology, Inc. Real estate listings held by brokerage firms other than this site owner are marked with the IDX logo. “WRIST, Inc. deems information reliable but not guaranteed. The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p style="font-style: italic;">Copyright ' . date('Y') . ' of Western Regional Information Systems and Technology, Inc. All rights reserved. </p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Western Regional Information Systems img src Technology Inc Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/wrist-logo.jpg" border="0" width="60px" style="float: right;" />';

if (in_array($_GET['load_page'], array('details', 'map', 'directions', 'local'))) {

	// Listing Details, Display Agent Name
	$_COMPLIANCE['details']['show_agent'] = true;

	// Listing Details, Display Office Name
	$_COMPLIANCE['details']['show_office'] = true;

}
