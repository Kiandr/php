<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Regional Multiple Listing Service of Oregon Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rmls.jpg" border="0" style="float: left; margin: 10px 10px 0 10px;" > The content relating to real estate for sale on this site comes in part from the IDX program of the RMLS&trade; of Portland, Oregon. Real Estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the RMLS&trade; logo, and detailed information about these properties include the name of the listing\'s broker. Listing content is copyright &copy; ' . date('Y') . ' RMLS&trade; of Portland, Oregon.';
    $_COMPLIANCE['disclaimer'][] = '<br /><br />All information provided is deemed reliable but is not guaranteed and should be independently verified.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Some properties which appear for sale on this web site may subsequently have sold or may no longer be available.</p>';

}

// Search Results: Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Regional Multiple Listing Service of Oregon Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rmls-thumb.jpg" border="0" >';

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye'));

// Brochure Logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/rmls.jpg';
$_COMPLIANCE['logo_width'] = 15;	// Width (FPDF)
$_COMPLIANCE['logo_location'] = 1;	// Placement
