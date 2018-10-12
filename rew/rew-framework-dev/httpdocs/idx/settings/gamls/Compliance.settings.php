<?php

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
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Georgia MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gamls.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" /> The data relating to real estate for sale on this web site comes in part from the Broker Reciprocity Program of Georgia MLS. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the Broker Reciprocity logo and detailed information about them includes the name of the listing brokers.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information Deemed Reliable But Not Guaranteed.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The broker providing this data believes it to be correct, but advises interested parties to confirm them before relying on them in a purchase decision.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date("Y") . ' Georgia MLS. All rights reserved.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Georgia MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/brgamls.jpg" width="16px" height="16px" border="0" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;

// Brochure Logo
$_COMPLIANCE['logo'] = 'img/logos/gamls.jpg';
$_COMPLIANCE['logo_width'] = 15;
$_COMPLIANCE['logo_location'] = 1;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
