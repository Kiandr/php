<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', 'dashboard'))) {



    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All listing information is deemed reliable but not guaranteed and should be independently verified through personal inspection by appropriate professionals. Listings displayed on this website may be subject to prior sale or removal from sale; availability of any listing should always be independently verified.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing information is provided for consumer personal, non-commercial use, solely to identify potential properties for potential purchase; all other use is strictly prohibited and may violate relevant federal and state law.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing data comes from My Florida Regional MLS.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details', 'brochure'))) ? true : false;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
