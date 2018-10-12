<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = 'This information is deemed reliable but not guaranteed.  You should rely on this information only to decide whether or not to further investigate a particular property.  BEFORE MAKING ANY OTHER DECISION, YOU SHOULD PERSONALLY INVESTIGATE THE FACTS (e.g. square footage and lot size) with the assistance of an appropriate professional. ';
    $_COMPLIANCE['disclaimer'][] = 'You may use this information only to identify properties you may be interested in investigating further. ';
    $_COMPLIANCE['disclaimer'][] = '</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = 'All uses except for personal, non-commercial use in accordance with the foregoing purpose are prohibited.  Redistribution or copying of this information, any photographs or video tours is strictly prohibited. This information is derived from the Internet Data Exchange (IDX) service provided by Sandicor&reg;.  Displayed property listings may be held by a brokerage firm other than the broker and/or agent responsible for this display. ';
    $_COMPLIANCE['disclaimer'][] = 'The information and any photographs and video tours and the compilation from which they are derived is protected by copyright.  Compilation &copy; ' . date('Y') . ' Sandicor&reg;, Inc. ';
    $_COMPLIANCE['disclaimer'][] = '</p>';
}

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = false;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = false;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = false;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}