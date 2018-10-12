<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('details','brochure'))) {

	// The following disclaimer is required on Details and Print Pages
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being'
		.' provided by the Greater El Paso Association of REALTORS&reg; is'
		.' exclusively for consumers\' personal, non-commercial use, and it'
		.' may not be used for any purpose other than to identify'
		.' prospective properties consumers may be interested in purchasing.'
		.' The data is deemed reliable but is not guaranteed accurate by'
		.' the MLS.</p>';

	// The listing office is required on the details and print pages of the IDX.
	$_COMPLIANCE['details']['show_office'] = true;
}

$_COMPLIANCE['results']['show_icon'] = '<img alt="Greater El Paso Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/gepar.jpg" border="0" />';

// ListTrac tracking
$_COMPLIANCE['backend']['always_show_idx_agent'] = true;
if (in_array($_GET['load_page'], array('register', 'details', 'friend'))) {
    $container_compliance = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class);
    $_COMPLIANCE['tracking']['ListTrac'] = [$container_compliance::ListTracID, ['ListingMLS', 'AddressZipCode']];
}