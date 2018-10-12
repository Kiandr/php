<?php

// All displays of Content shall include the following disclaimer:
//
//     This information is not verified for authenticity or accuracy and is not guaranteed. This information is
//     being provided for consumers' personal, non-commerical use and may not be used for any purpose other
//     than to identify prospective property to purchase. © 1976 - 2011 Hilton Head Island
//     Multiple Listing Service, Inc. All rights reserved.
//
// The listing office is required on the search results, details pages, and map pop ups.
//
// If a thumbnail view of listings Content is provided on the site, then the Member must display the HHIMLS
// logo on each listing not owned by the Member with an indication that the logo represents listings not
// owned by the displaying Member. All detailed Content must also display the HHIMLS logo indicating that
// the listing is not owned by the displaying Member. On each page displaying the Content with the HHIMLS
// logo and the statement Courtesy of: (the listing Real Estate Brokerage) and additional disclosures must
// be displayed on all formats other than the thumbnail format.

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img style="float: right;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/hhimls.jpg" />We do not attempt to independently verify the currency, completeness, accuracy or authenticity of the data contained herein. It may be subject to transcription and transmission errors. Accordingly, the data is provided on an "as is," "as available" basis only and may not reflect all real estate activity in the market. &copy; ' . date('Y') . ' Multiple Listing Service of Hilton Head Island, Inc. All rights reserved. Certain information contained herein is derived from information which is the licensed property of, and copyrighted by, Multiple Listing Service of Hilton Head Island, Inc. </p>';
}

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img width="50" height="40" style="float:left;" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/hhimls.jpg" border="0" />';

$_COMPLIANCE['details']['lang']['provider'] = 'Courtesy of ';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/hhimls.jpg';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// Search Result Limit
$_COMPLIANCE['limit'] = 500;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
