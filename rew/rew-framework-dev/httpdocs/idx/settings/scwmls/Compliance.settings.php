<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'DISCLAIMER: Information is supplied by seller and other third parties and has not been verified.';
	$_COMPLIANCE['disclaimer'][] = 'The IDX information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.<br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'Data provided by the South Central Wisconsin MLS as of ' . date('m/d/Y'). '.<br /><br />';
	$_COMPLIANCE['disclaimer'][] = 'Copyright ' . date('Y') . ' &ndash; (South Central Wisconsin MLS) &ndash; All Rights Reserved';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Provider Below Remarks
$_COMPLIANCE['details']['show_below_remarks'] = true;
