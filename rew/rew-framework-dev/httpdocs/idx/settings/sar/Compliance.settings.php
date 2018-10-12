<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Summit Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/sar-logo.jpg" border="0" style="float: left; margin: 5px 5px 5px 0px;" />';
	$_COMPLIANCE['disclaimer'][] = 'Copyright &copy; ' . date('Y') . ' SAR MLS. The information displayed herein was derived from sources believed to be accurate, but has not been verified by SAR MLS. ';
	$_COMPLIANCE['disclaimer'][] = 'Buyers are cautioned to verify all information to their own satisfaction. ';
	$_COMPLIANCE['disclaimer'][] = 'This information is exclusively for viewers\' personal, non-commercial use. Any republication or reproducion of the information herein without the express permission of the SAR MLS is strictly prohibited. ';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Summit Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/sar-logo.jpg" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Print Brochure
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/sar-logo.jpg';
$_COMPLIANCE['logo_width'] = 12;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
