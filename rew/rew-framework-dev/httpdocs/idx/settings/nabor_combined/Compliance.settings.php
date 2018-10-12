<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	// MLS Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	if (in_array($_GET['load_page'], array('search', '', 'map', 'dashboard'))) {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated));?>  and updating occurs daily</p>';
	}
	if (in_array($_GET['load_page'], array('details', 'brochure'))) {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Naples Area Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" alt="br Logo" border="0" /> The source of this real property information is the copyrighted and proprietary database compilation of the Southwest Florida MLS organizations Copyright '.date('Y').' Southwest Florida MLS organizations.. All rights reserved. The accuracy of this information is not warranted or guaranteed. This information should be independently verified if any person intends to engage in a transaction in reliance upon it. </p>';
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this Website come in part from the Broker Reciprocity Program (BR Program) of M.L.S. of Naples, Inc. Properties listed with brokerage firms other than ' . $broker_name . ' are marked with the BR Program Icon or the BR House Icon and detailed information about them includes the name of the Listing Brokers. The properties displayed may not be all the properties available through the BR Program. </p>';
	} else {
		$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Naples Area Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" alt="br Logo" border="0" /> The data relating to real estate for sale on this Website come in part from the Broker Reciprocity Program (BR Program) of M.L.S. of Naples, Inc. Properties listed with brokerage firms other than ' . $broker_name . ' are marked with the BR Program Icon or the BR House Icon and detailed information about them includes the name of the Listing Brokers. The properties displayed may not be all the properties available through the BR Program. </p>';
	}
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Search Results, Display MLS Number
$_COMPLIANCE['results']['show_mls'] = true;

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Naples Area Board of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-house.gif" border="0" >';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map','local'));

// Brochure Logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';
$_COMPLIANCE['logo_width'] = 17;   // Logo Width (FPDF)
$_COMPLIANCE['logo_location'] = 1; // Logo Position
