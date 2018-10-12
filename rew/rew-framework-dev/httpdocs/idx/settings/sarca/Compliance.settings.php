<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

//A listing limit of 500 per search should be in place, with a statement mentioning this.
$_COMPLIANCE['limit'] = 500;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information'
		.' being provided by the Shasta Association of REALTORS&reg; is'
		.' exclusively for consumers\' personal, non-commercial use, and'
		.' it may not be used for any purpose other than to identify'
		.' prospective properties consumers may be interested in'
		.' purchasing. The data is deemed reliable but is not guaranteed'
		.' accurate by the MLS.</p>';

}

//The listing agent and office are required on the details and print pages.
if (in_array($_GET['load_page'], array('details', 'brochure'))) {
	$_COMPLIANCE['details']['show_agent'] = true;
	$_COMPLIANCE['details']['show_office'] = true;
}
