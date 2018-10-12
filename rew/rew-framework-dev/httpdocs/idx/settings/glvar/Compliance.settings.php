<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Greater Las Vegas Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/glvar.gif" style="float: left; margin: 10px 15px 5px 0px;" border="0" /> The data relating to real estate for sale on this web site comes in part from the INTERNET DATA EXCHANGE Program of the Greater Las Vegas Association of REALTORS&reg; MLS. Real estate listings held by brokerage firms other than this site owner are marked with the IDX logo.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided is for the consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><em>Copyright ' . date('Y') . ' of the Greater Las Vegas Association of REALTORS&reg; MLS. All rights reserved.</em></p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">GLVAR deems information reliable but not guaranteed.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Greater Las Vegas Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/glvar.gif" border="0" style="margin: 5px 5px 0px 5px;" />';

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Page Footer Compliance
$_COMPLIANCE['footer'] = '<a href="http://www.lasvegasrealtor.com/wp-content/uploads/2015/11/Exhibit-A.pdf">DMCA Copyright Infringement Clause</a>';

// Listing Brochure, Display Thumbnail Icon
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . "logos/glvar.gif";

// Listing Brochure, Location of Thumbnail Icon
$_COMPLIANCE['logo_location'] = 1;

// Listing Brochure, Width of Thumbnail Icon
$_COMPLIANCE['logo_width'] = 16;
