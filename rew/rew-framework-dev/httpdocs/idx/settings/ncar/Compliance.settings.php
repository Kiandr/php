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
}

// Disclaimer
if (in_array($_GET['load_page'], array('details', 'brochure'))) {
		$str = 'The information on this form is from varying sources.  Neither the brokers nor the agents nor the Nevada County Association of REALTORS&reg; Multiple Listing Service have verified it.  Buyer is advised to consult with appropriate professional experts.<br /><br /> The information being provided is for consumers\' personal, non-commercial use, and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.<br /><br />Data provided by Nevada County Association of REALTORS&reg; Electronic Data Display.<br /><br />';
} else {
	
	$str = 'The information on this form is from varying sources.  Neither the brokers nor the agents nor the Nevada County Association of REALTORS&reg; Multiple Listing Service have verified it.  Buyer is advised to consult with appropriate professional experts.<br /><br /> The information being provided is for consumers\' personal, non-commercial use, and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.<br /><br />Data provided by Nevada County Association of REALTORS&reg; Electronic Data Display.<br /><br />';
}

$_COMPLIANCE['disclaimer'][] = $str;
//  Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
//Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;
// Search Results, Display Provider Above View Listing Button
$_COMPLIANCE['results']['show_above_actions'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));

$_COMPLIANCE['results']['lang']['provider'] = 'Listing Office: ';
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Office: ';
 
