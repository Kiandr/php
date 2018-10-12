<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

// Disclaimer
$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Intermountain MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/imls.png" border="0" style="float: left; margin: 0 15px 0.5em 0;" />IDX information is provided exclusively for consumers\' personal, non-commercial use, and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. IMLS does not assume any liability for missing or inaccurate data.  Information provided by IMLS is deemed reliable but not guaranteed.</p>';

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Intermountain MLS Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/imls.png" border="0" width="30" />';

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));

// Brochure Compliance
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/imls.png';
$_COMPLIANCE['logo_width'] = 20; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
