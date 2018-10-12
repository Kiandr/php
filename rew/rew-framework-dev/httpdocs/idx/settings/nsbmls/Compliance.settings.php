<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information Courtesy of'
		. ' the New Smyrna Beach Board of REALTORS&reg; Multiple Listing Service IDX'
		. ' information is provided exclusively for consumers’ personal, non-commercial'
		. ' use, and may not be used for any purpose other than to identify prospective'
		. ' properties consumers may be interested in purchasing.  The data is deemed'
		. ' reliable but is not guaranteed accurate by the MLS.</p>';

}


// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details','brochure'));

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = true;
