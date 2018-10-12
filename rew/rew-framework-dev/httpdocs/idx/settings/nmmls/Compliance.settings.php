<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Require Account Data
	$broker_name = '[INSERT BROKER HERE]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Emmet Association of Realtors (Northern Michigan MLS) Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nmmls.jpg" border="0" style="float: left; margin: -3px 5px 0 -3px;" />The data relating to real estate on this web site comes in part from the Internet Data Exchange Program of the Northern Michigan MLS (NM-MLSX).  Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the NM-MLSX  logo and the detailed information about said listing includes the listing office. All information deemed reliable but not guaranteed and should be independently verified.  All properties are subject to prior sale, change or withdrawal.  Neither the listing broker(s) nor ' . $broker_name . ' shall be responsible for any typographical errors, misinformation, misprints, and shall be held totally harmless.  Northern Michigan MLS, Inc &copy; All rights reserved. IDX information is provided exclusively for consumers\' personal, non-commercial use, that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details','brochure'));
$_COMPLIANCE['details']['lang']['provider'] = 'This (property or listing) is courtesy of ';
