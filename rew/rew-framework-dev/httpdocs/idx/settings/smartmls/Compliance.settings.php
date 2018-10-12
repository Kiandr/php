<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '2017 Listings courtesy of the SMART MLS<br>';
	$_COMPLIANCE['disclaimer'][] = 'IDX information is provided exclusively for consumers’ personal, non-commercial use, that it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing, and that the data is deemed reliable but is not guaranteed accurate by the MLS.';
	$_COMPLIANCE['disclaimer'][] = 'Listing information is updated every fifteen minutes.<br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'Listing data is deemed reliable but is not guaranteed accurate by the MLS.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

//Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

//Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;

//Saved searches max 200 listings
$_COMPLIANCE['limit'] = 200;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
