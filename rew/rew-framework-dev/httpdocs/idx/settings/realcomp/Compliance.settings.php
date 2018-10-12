<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 250;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Account Data
	$broker_name  = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Realcomp II Ltd Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/realcom.gif" border="0" style="float: left; margin: 0 15px 0.5em 0;" />  ';
	$_COMPLIANCE['disclaimer'][] = 'Provided through IDX through Realcomp II Ltd. Courtesy of ' . $broker_name . '. Copyright ' . date('Y') . ' Realcomp II Ltd. Shareholders. IDX information is provided exclusively for consumers\' personal, noncommercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The accuracy of all information, regardless of source, is not guaranteed or warranted. All information should be independently verified.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

// Listing Details, Display Office Name
if (!in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'directions', 'local'))) {
	$_COMPLIANCE['details']['show_office'] = true;
}

// Listing Brochure, Display IDX Logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/realcom.gif';
$_COMPLIANCE['logo_location'] = 1;
$_COMPLIANCE['logo_width'] = 17;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
