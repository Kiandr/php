<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being'
		. ' provided by the Rockport Area Association of REALTORS is exclusively'
		. ' for consumers\' personal, non-commercial use, and it may not be used'
		. ' for any purpose other than to identify prospective properties consumers'
		. ' may be interested in purchasing. The data is deemed reliable but is not'
		. ' guaranteed accurate by the MLS.</p>';

}

// IDX Details, Display Office
$_COMPLIANCE['details']['show_office'] = true;

// Featured Listings, Display Office
$_COMPLIANCE['featured']['show_office'] = true;

// Search Results, Display Office
$_COMPLIANCE['results']['show_office'] = true;
