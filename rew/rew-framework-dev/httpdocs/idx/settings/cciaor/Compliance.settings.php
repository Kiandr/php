<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

// Require Broker Name
$broker_name = '[INSERT BROKER NAME]';

if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['disclaimer'][] = '<p>Listing Courtesy of <?= $listing[\'ListingOffice\']; ?></p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Cape Cod img src Islands Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']  . 'logos/br-logo-newest.jpg" width="150" style="width:150px; float:left">' . 'All data relating'
		. ' to real estate for sale on this page comes from the Broker Reciprocity (BR)'
		. ' of the Cape Cod & Islands Multiple Listing Service, Inc. Detailed information'
		. ' about real estate listings help by brokerage firms other than '
		. $broker_name . ' include the name of the listing broker. Neither the'
		. ' listing company nor ' . $broker_name . ' shall be responsible for'
		. ' any typographical errors, misinformation or misprints and shall be held'
		. ' totally harmless.  The Broker providing this data believes it to be'
		. ' correct, but advises interested parties to confirm any item before'
		. ' relying on it in a purchase decision. Copyright ' . date("Y") . ' &copy; Cape Cod'
		. ' &amp; Islands Multiple Listing Service, Inc. All rights reserved.</p>';
} else if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'map', 'streetview', 'birdseye', 'directions', 'local', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. '<img alt="Cape Cod img src Islands Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']  . 'logos/br-logo-newest.jpg" width="150" style="width:150px; float:left">' . 'The data relating'
		. ' to real estate for sale on this site comes from the Broker Reciprocity'
		. ' (BR) of the Cape Cod & Islands Multiple Listing Service, Inc.  Summary'
		. ' or thumbnail real estate listings held by brokerage firms other than'
		. ' ' . $broker_name . ' are marked with the BR Logo and detailed'
		. ' information about them includes the name of the listing broker. Neither'
		. ' the listing broker nor ' . $broker_name . ' shall be responsible'
		. ' for any typographical errors, misinformation or misprints and shall be'
		. ' held totally harmless. This site was last updated <?=date(\'m/d/Y\', strtotime($last_updated)); ?>.'
		. ' All properties are subject to prior sale, changes or withdrawal.</p>';
}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Cape Cod img src Islands Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']  . 'logos/br-logo-newest.jpg" width="125" style="width:125px;">';

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/br-logo-newest.jpg';
$_COMPLIANCE['logo_width'] = 35; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1);

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
