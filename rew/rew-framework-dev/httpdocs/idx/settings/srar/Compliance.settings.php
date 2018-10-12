<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

$_COMPLIANCE['limit'] = 200;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'The Saskatoon Region Association of REALTORS&reg; (SRAR) IDX Reciprocity listings are displayed in accordance with SRAR\'s ' . Lang::write('MLS') . ' Data Access Agreement and are copyright of the Saskatoon Region Association of REALTORS&reg;. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The above information is from sources deemed reliable but should not be relied upon without independent verification. The information presented here is for general interest only, no guarantees apply. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">' . Lang::write('MLS') . ', REALTOR&reg;, and the associated logos are trademarks of The Canadian Real Estate Association.';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));

// Custom MLS Compliance fields (Office & Agent Phone)
$_COMPLIANCE['details']['extra'] = function ($idx, $db_idx, $listing, $_COMPLIANCE) {
	return array(
		array(
			'heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ? $_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'),
			'fields' => array(
				!empty($_COMPLIANCE['details']['show_agent']) ? array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
				!empty($_COMPLIANCE['details']['show_office']) ? array('title' => (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'), 'value' => 'ListingOffice', 'attributes' => 'style="font-size: 16px;"') : null,
			),
		),
	);
};
