<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap' , 'dashboard'))) {

    // Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">The data relating to real estate for sale on this website comes in part from the Internet Data Exchange Program of the Williamsburg Multiple Listing Service, Inc. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Internet Data Exchange logo or the Internet Data Exchange brief/thumbnail logo and detailed information about them includes the name of the listing firms. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">The property data as provided by ' . $broker_name . ' is believed to be correct, however, interested parties are advised to confirm the information prior to making a purchase decision. </p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">Copyright ' . date('Y') . ' Williamsburg Multiple Listing Service, Inc. All rights reserved. </p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Williamsburg Area Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/waar-logo.jpg" border="0" />';


// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
