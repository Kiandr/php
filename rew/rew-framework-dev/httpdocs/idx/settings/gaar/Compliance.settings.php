<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('dashboard', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', '', 'sitemap'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Greater Albuquerque Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gaar.gif" style="float: left; margin: 10px; " border="0" /> Some of the information contained herein has been provided by SWMLS, Inc. This information is from sources deemed reliable but not guaranteed by SWMLS, Inc. The information is for consumers\' personal, non-commercial use and may not be used for any purpose other than identifying properties which consumers may be interested in purchasing.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gaar.gif';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
