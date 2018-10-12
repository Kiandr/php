<?php

// Thumbnail Listings
//  - 2 or less lines of text and an image no bigger than 150px high
//  - must use a disclaimer similar to the following and display the GCMLS-approved icon or thumbnail icon
//    (not an approved disclaimer): Listings marked with the GCMLS-approved icon or GCMLS-approved thumbnail icon refer to listings of other IDX members and are provided coutesy of the GCMLS IDX Database.
// 
//  Also, as discussed in the compliance Rules and Regs document, sites displaying less than the full set of listings from the feed should probably have a disclaimer noting that

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    // Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = '<img alt="Columbia Greene Northern Dutchess MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gcmls.jpg" border="0" style="float: left; margin: -12px 10px 0 0;" /> ';
    $_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the IDX Program of the GCMLS. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the IDX logo or the IDX thumbnail logo and detailed information about them includes the name of the listing brokers. ';
    $_COMPLIANCE['disclaimer'][] = 'The broker providing these data believes them to be correct, but advises interested parties to confirm them before relying on them in a purchase decision. ';
    $_COMPLIANCE['disclaimer'][] = '&copy; ' . date("Y") . ' GCMLS. All rights reserved. ';
    $_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Columbia Greene Northern Dutchess MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gcmls_sm.jpg" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
