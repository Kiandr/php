<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 400;

if (in_array($_GET['load_page'], array('search'))) {

    // Display Update Time
    $_COMPLIANCE['update_time'] = true;

}

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

    $_COMPLIANCE['disclaimer'][] = '<div>';
    $_COMPLIANCE['disclaimer'][] = '<p>&copy; Copyright ' . date('Y') . ' of the SFAR MLS.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p>Listings on this page identified as belonging to another listing firm are based upon data obtained from the SFAR MLS, which data is copyrighted by the San Francisco Association of REALTORS&reg;, but is not warranted.  Information being provided is for consumers\' personal, noncommercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
    $_COMPLIANCE['disclaimer'][] = '</div>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
