<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information offered by The Greater Alabama MLS&reg;, Inc. is provided exclusively for consumers\' personal, non-commercial use, it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing, and the content is not guaranteed accurate by the MLS&reg;</p>';
	$_COMPLIANCE['disclaimer'][] = '</div>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

$_COMPLIANCE['details']['above_inquire'] = true;

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}
