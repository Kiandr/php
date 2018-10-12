<?php

// Global Compliance
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array();

// Only Show On Certain Pages
if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

    // Require Account Data
    $broker_name = '[INSERT BROKER NAME]';

    // MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Hudson Gateway MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/hgmls.jpg" border="0" style="float: left; margin: 5px 10px 0px;" /> The data relating to real estate for sale or lease on this web site comes in part from HGMLS. Real estate listings held by brokerage firms other than ' . $broker_name . ' are  marked with the HGMLS logo or an abbreviated logo and detailed information about them includes the name of the listing broker.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information deemed reliable but not guaranteed.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy; ' . date('Y') . ' Hudson Gateway Multiple Listing Service, Inc. All rights reserved.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Hudson Gateway MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/hgmls.jpg" border="0" alt="HGMLS" style="height: 40px;"><br>';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details','brochure'))) ? true : false;
$_COMPLIANCE['details']['lang']['provider'] = "Listing Courtesy of";

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/hgmls.jpg';
$_COMPLIANCE['logo_width'] = 20;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement
