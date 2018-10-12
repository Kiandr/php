<?php

// IDX Compliance
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// MLS Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">This website may only be used by consumers that have a bona fide interest in the purchase, sale, or lease of real estate of the type being offered via the website. The data is deemed reliable but is not guaranteed accurate by TREB.</p>';

}

// Search Results: Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'map') ? true : false;
