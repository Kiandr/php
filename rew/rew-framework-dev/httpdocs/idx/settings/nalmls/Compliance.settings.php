<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	    // Disclaimer
		    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="North Alabama MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nalmls.jpg" border="0" align="left" style="margin:-4px 5px 0px 0px" />Properties marked with the NALMLS icon are provided courtesy of the North Alabama Multiple Listing Service, Inc. (NALMLS) IDX Database. All information provided is deemed reliable but is not guaranteed and should be independently verified. Copyright NALMLS, Inc.</p>';

}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="North Alabama MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nalmls_sm.jpg" border="0" width="60" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nalmls.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
