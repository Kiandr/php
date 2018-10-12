<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 400;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information source: Information and Real Estate Services, LLC. Provided for limited non-commercial use only under IRES Rules. &copy; Copyright IRES. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information deemed reliable but not guaranteed by the MLS. </p>';
}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details','brochure'))) ? true : false;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
