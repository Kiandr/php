<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = false;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

    $_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
    $_COMPLIANCE['disclaimer'][] = '<img alt="Greater Louisville Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/GLAR_small.gif" border="0" />';
    $_COMPLIANCE['disclaimer'][] = '<p>The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange Program of Metro Search, Inc. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Internet Data Exchange logo or the Internet Data Exchange thumbnail logo and detailed information about each listing includes the name of the listing broker.</p>';
    $_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Greater Louisville Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/GLAR_small.gif" border="0" />';

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = false;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = false;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (!in_array($_GET['load_page'], array('map', 'brochure')) ? true : false);
