<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright &copy; ' . date("Y") . ','
		.' Columbus Board of REALTORS&reg; MLS. All information provided is'
		.' deemed reliable but is not guaranteed and should be independently verified.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The IDX information is'
		.' provided exclusively for consumers\' personal, non-commercial use,'
		.' it may not be used for any purpose other than to identify prospective'
		.' properties consumers may be interested in purchasing. All information'
		.' provided is deemed reliable but is not guaranteed accurate by the MLS'
		.' and should be independently verified.</p>';

}

$_COMPLIANCE['results']['lang']['provider'] = 'Listing Courtesy ';
$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy ';
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['details']['show_below_remarks'] = true;
