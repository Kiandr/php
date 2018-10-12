<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer
$_COMPLIANCE['disclaimer'] = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
        if (in_array($_GET['load_page'], array('details'))) {
                $_COMPLIANCE['disclaimer'][] = '<p><img alt="Central Jersey MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cjmls.jpg" border="0" width="auto" height="70" style="height: 70px;"></p>';
        }
        $_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web-site comes in part from the Internet Listing Display database of the CENTRAL JERSEY MULTIPLE LISTING SYSTEM, INC. ';
        $_COMPLIANCE['disclaimer'][] = 'Real estate listings held by brokerage firms other than this site-owner are marked with the ILD logo. ';
        $_COMPLIANCE['disclaimer'][] = 'The CENTRAL JERSEY MULTIPLE LISTING SYSTEM, INC does not warrant the accuracy, quality, reliability, suitability, completeness, usefulness or effectiveness of any information provided. ';
        $_COMPLIANCE['disclaimer'][] = 'The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify properties the consumer may be interested in purchasing or renting. ';
        $_COMPLIANCE['disclaimer'][] = 'Copyright ' . date("Y") . ', CENTRAL JERSEY MULTIPLE LISTING SYSTEM, INC. All rights reserved. ';
        $_COMPLIANCE['disclaimer'][] = 'The CENTRAL JERSEY MULTIPLE LISTING SYSTEM, INC retains all rights, title and interest in and to its trademarks, service marks and copyrighted material. ';
}

// Results Page Compliance
$_COMPLIANCE['results']['show_icon'] = '<img alt="Central Jersey MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cjmls.jpg" border="0" height="50" style="height: 50px;">';

// Details Page Compliance Info
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of';
$_COMPLIANCE['provider']['above_inquire'] = true;
$_COMPLIANCE['details']['above_inquire'] = true;

// Brochure - show office
if(in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Brochure Info
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cjmls.jpg';
$_COMPLIANCE['logo_location'] = 1;
$_COMPLIANCE['logo_width'] = 28;

