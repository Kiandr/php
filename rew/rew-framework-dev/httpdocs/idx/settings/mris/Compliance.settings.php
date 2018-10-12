<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', '', 'dashboard'))) {

    // Client's Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer Text
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Metropolitan Regional Information System Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mris.gif" border="0" class="floated" style="margin: 15px 10px 5px 0px;" />';
    $_COMPLIANCE['disclaimer'][] = 'The listing content relating to real estate for sale on this web site is courtesy of MRIS. Listing information comes from various brokers who participate in the MRIS IDX.';
    $_COMPLIANCE['disclaimer'][] = 'Properties listed with brokerage firms other than ' . $broker_name . ' are marked with the MRIS Logo and detailed information about them includes the name of the listing brokers.';
    $_COMPLIANCE['disclaimer'][] = 'The properties displayed may not be all the properties available. All information provided is deemed reliable but is not guaranteed and should be independently verified.';
    $_COMPLIANCE['disclaimer'][] = 'All listing information copyright MRIS ' . date('Y') . '.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details','brochure'))) ? true : false;

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mris.gif';
$_COMPLIANCE['logo_width'] = 25; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key

?>