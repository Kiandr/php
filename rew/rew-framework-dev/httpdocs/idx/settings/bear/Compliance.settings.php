<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = "<p class='disclaimer'>";
	$_COMPLIANCE['disclaimer'][] = '<img alt="Bay East Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/combologo.jpg" border="0" style="float: left;" width="115" height="59"> ';
	 $_COMPLIANCE['disclaimer'][] = '<p>Bay East &copy;' . date('Y') . '. CCAR &copy;' . date('Y') . '. BridgeMLS &copy;' . date('Y') . '. Information deemed reliable but not guaranteed.<br />
	This information is being provided by the Bay East MLS or the CCAR MLS or the EBRDI MLS. The listings presented here may or may not be listed by the Broker/Agent operating this website.</p>';

}
$_COMPLIANCE['results']['show_icon'] = '<img alt="Bay East Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/combologo.jpg" height="25" border="0" style="height: 25px;" />';

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/combologo.jpg';

$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
