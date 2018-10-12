<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    // Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Northern Nevada Regional Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" /> The data relating to real estate for sale on this web site comes in part from the BROKER RECIPROCITY Program of the Northern Nevada Regional MLS. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the BROKER RECIPROCITY logo or the BROKER RECIPROCITY thumbnail logo and detailed information about them includes the name of the listing brokerage. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The broker providing these data believes them to be correct, but advises interested parties to confirm them before relying on them in a purchase decision. </p>';
	
    if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy; ' . date("Y") . ' of Northern Nevada Regional MLS. All rights reserved.</p>';
    }

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Northern Nevada Regional Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'birdseye', 'local', 'streetview'));

// Listing Brochure, Display Thumbnail Icon
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br.jpg';
$_COMPLIANCE['logo_width'] = 23;
$_COMPLIANCE['logo_location'] = 1;

// Determine Whether Listing Is Up For Auction
$_COMPLIANCE['is_up_for_auction'] = function ($listing) {
    return (strpos(strtolower($listing['ListingSpecialConditionOfSale']), 'auction') !== false);
};
