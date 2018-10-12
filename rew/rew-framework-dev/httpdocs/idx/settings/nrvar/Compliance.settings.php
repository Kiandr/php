<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">The data relating to'
		.' real estate on this website comes in part from the Broker Reciprocity'
		.'/IDX (Internet Data Exchange) Program of the New River Valley Multiple'
		.' Listing Service, Inc. Real estate listings held by brokerage firms other'
		.' than '.$broker_name.' are marked with the Broker Reciprocity'
		.' logo (IDX) and detailed information about them includes the name of the'
		.' listing broker. The IDX information is provided exclusively for consumers'
		.' personal, non-commercial use and may not be used for any purpose other'
		.' than to identify prospective properties consumers may be interested in'
		.' purchasing. The data is deemed reliable but is not guaranteed accurate'
		.' by the MLS.</p>';

}

$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['results']['show_office'] = true;
