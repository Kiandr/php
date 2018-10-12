<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer']   = array('');

// Show on Certain Pages
if (in_array($_GET['load_page'], array('details', 'brochure'))) {

    // Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer Text
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All listings featuring the icon <img alt="Northwest Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nwmls.png" border="0" /> are provided courtesy of the Northwest Multiple Listing Service (NWMLS), Copyright ' . date('Y') . '. All rights reserved. ';
    $_COMPLIANCE['disclaimer'][] = 'The database information herein is provided from and copyrighted by the Northwest Multiple Listing Service (NWMLS). ';
    $_COMPLIANCE['disclaimer'][] = 'NWMLS data may not be reproduced or redistributed and is only for people viewing this site. ';
    $_COMPLIANCE['disclaimer'][] = 'All information provided is deemed reliable but is not guaranteed and should be independently verified. ';
    $_COMPLIANCE['disclaimer'][] = 'The information contained in these listings has not been verified by ' . $broker_name . ' and should be verified by the buyer. ';
    $_COMPLIANCE['disclaimer'][] = 'All properties are subject to prior sale or withdrawal. All rights are reserved by copyright. ';
    $_COMPLIANCE['disclaimer'][] = 'Property locations as displayed on any map are best approximations only and exact locations should be independently verified.</p>';

}

// Restrict Featured Listings
$_COMPLIANCE['featured']['office_id'] = ''; // Comma-separated ListingOfficeIDs from IDX

// Search Results, Display Thumbnail Icon
$_COMPLIANCE['results']['show_icon'] = '<img alt="Northwest Multiple Listing Service Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nwmls.png" border="0" />';

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'birdseye', 'local', 'streetview')) ? true : false;

// Listing Details, Display Office Below Remarks
$_COMPLIANCE['details']['show_below_remarks'] = true;
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Office: ';

// Brochure logo
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/nwmls.png';
$_COMPLIANCE['logo_width'] = 5;    // Width (FPDF)
$_COMPLIANCE['logo_location'] = 1; // Placement

// Page Footer Compliance
$_COMPLIANCE['footer'] = '<a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'copyright.html">NWMLS Copyright Infringement Information</a>';

// NWMLS Copyright Information
$_COMPLIANCE['pages']['copyright'] = array();
$_COMPLIANCE['pages']['copyright']['page_title']     = 'NWMLS Copyright Information';
$_COMPLIANCE['pages']['copyright']['category_html']  = '<h1>NWMLS Copyright Infringement Info</h1>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<h3>Claims of Copyright Infringement &amp; Related Issues (17 USC &sect; 512 et seq.)</h3>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>We respect the intellectual property rights of others. Anyone who believes their work has been reproduced in a way that constitutes copyright infringement may notify our agent by providing the following information:</p>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<ul>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>Identification of the copyrighted work that you claim has been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at the site;</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>Identification of the material that you claim is infringing and needs to be removed, including a description of where it is located so that the copyright agent can locate it;</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>Your address, telephone number, and, if available, e-mail address, so that the copyright agent may contact you about your complaint; and</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>A signed statement that the above information is accurate; that you have a good faith belief that the identified use of the material is not authorized by the copyright owner, its agent, or the law; and, under penalty of perjury, that you are the copyright owner or are authorized to act on the copyright owner\'s behalf in this situation.</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= 'Upon obtaining such knowledge we will act expeditiously to remove, or disable access to, the material. Please be aware that there are substantial penalties for false claims.</ul>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>If a notice of copyright infringement has been wrongly filed against you, you may submit a counter notification to our agent. A valid counter notification is a written communication that incorporates the following elements:</p>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<ul>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>A physical or electronic signature of the poster;</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>Identification of the material that has been removed or to which access has been disabled and the location at which the material appeared before it was removed or access to it was disabled;</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>A statement under penalty of perjury that you have a good faith belief that the material was removed or disabled as a result of mistake or misidentification;</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<li>Your name, address, and telephone number; a statement that you consent to the jurisdiction of federal district court for the judicial district in which your address is located, or if your address is outside of the U.S., for any judicial district in which the service provider may be found; and that you will accept service of process from the complainant.</li>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '</ul>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>Notices of the foregoing copyright issues should be sent as follows:</p>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>By mail:<br />Northwest Multiple Listing Service<br />11430 NE 120th Street<br />Kirkland, WA 98034<br />United States<br />Attention: DMCA Designated Agent</p>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>By e-mail:<br /><a href="mailto:copyright@nwmls.com">copyright@nwmls.com</a></p>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>If you give notice of copyright infringement by e-mail, an agent may begin investigating the alleged copyright infringement; however, we must receive your signed statement by mail or as an attachment to your e-mail before we are required to take any action.</p>';
$_COMPLIANCE['pages']['copyright']['category_html'] .= '<p>This information should not be construed as legal advice. We recommend you seek independent legal counsel before filing a notification or counter-notification. For further information about the DMCA, please visit the website of the United States Copyright Office at:<br /><a href="http://www.copyright.gov/onlinesp">http://www.copyright.gov/onlinesp</a>.</p>';
