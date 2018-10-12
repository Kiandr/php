<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy' . date("Y") . ' Gainesville'
		.' Multiple Listing, Inc. All rights reserved. The information being'
		.' provided by the Gainesville Multiple Listing, Inc. is exclusively'
		.' for consumers\' personal, non-commercial use, and it may not be used'
		.' for any purpose other than to identify prospective properties'
		.' consumers may be interested in purchasing. The data is deemed'
		.' reliable but is not guaranteed accurate by the MLS.</p>';

}

$_COMPLIANCE['details']['above_inquire'] = true;
$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['results']['show_office'] = true;
