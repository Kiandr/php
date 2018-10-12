<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 200;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure'))) {

    // Display Update Time
    $_COMPLIANCE['update_time'] = true;

    $_COMPLIANCE['disclaimer'] = function ($listing_result) {
        $disclaimer = array('');

        $disclaimer[] = '<div>';

        if (strtolower($listing_result['SourceMLS']) === strtolower($listing_result['OriginatingMLS'])) {
            $disclaimer[] = '<p><img alt="MiRealSource Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mirealsource.png" srset="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mirealsource-retina.png 2x" border="0" style="float: left; margin: 5px 5px 5px 0px; width: 120px; height: 80px;" />Provided through IDX via MiRealSource. Courtesy of MiRealSource Shareholder. Copyright MiRealSource.</p>';
            $disclaimer[] = '<p>The information published and disseminated by MiRealSource is communicated verbatim, without change by MiRealSource, as filed with MiRealSource by its members. The accuracy of all information, regardless of source, is not guaranteed or warranted. All information should be independently verified.</p>';
        } else {
            $disclaimer[] = '<p><img alt="MiRealSource Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mirealsource.png" srset="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mirealsource-retina.png 2x" border="0" style="float: left; margin: 5px 5px 5px 0px; width: 120px; height: 80px;" />Provided through IDX via MiRealSource, as the "Source MLS", courtesy of the ' . $listing_result['OriginatingMLS'] . ' shown on the property listing, as the Originating MLS.</p>';
            $disclaimer[] = '<p>The information published and disseminated by the Originating MLS is communicated verbatim, without change by the Originating MLS, as filed with it by its members. The accuracy of all information, regardless of source, is not guaranteed or warranted. All information should be independently verified.</p>';
        }
        $disclaimer[] = '<p>Copyright '.date('Y').' MiRealSource. All rights reserved. The information provided hereby constitutes proprietary information of MiRealSource, Inc. and its shareholders, affiliates and licensees and may not be reproduced or transmitted in any form or by any means, electronic or mechanical, including photocopy, recording, scanning or any information storage and retrieval system, without written permission from MiRealSource, Inc.</p>';
        $disclaimer[] = '</div>';

        return $disclaimer;
    };
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['show_icon'] = '<img alt="MiRealSource Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mirealsource-thumb.png" style="width:60px; height:40px;">';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/mirealsource-retina.png';
$_COMPLIANCE['logo_width'] = 25; // Width (FPDF, not actual)
$_COMPLIANCE['logo_location'] = 1; // Paragraph key
$_COMPLIANCE['shift_paragraphs'] = array(1, 2);
