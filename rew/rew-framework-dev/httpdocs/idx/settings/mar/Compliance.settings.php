<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Require Account Data
	$broker_name = '[INSERT BROKER NAME]';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate on this web site comes in part from the Internet Data Exchange program of the MLS of the Miami Association of REALTORS&reg;, and is updated as of <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?> (date/time).</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All information is deemed reliable but not guaranteed by the MLS and should be independently verified. All properties are subject to prior sale, change, or withdrawal. Neither listing broker(s) nor ' . $broker_name . ' shall be responsible for any typographical errors, misinformation, or misprints, and shall be held totally harmless from any damages arising from reliance upon these data. &copy; ' . date('Y') . ' MLS of MAR.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));

// Listing Details Provider
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of';
