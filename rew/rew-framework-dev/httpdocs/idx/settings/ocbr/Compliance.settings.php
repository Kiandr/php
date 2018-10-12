<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information Source: Ocean County Board of REALTORS&reg;</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'local'))) ? false : true;
