<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to'
		. ' real estate on this web site comes in part from the Internet Data'
		. ' Exchange (IDX) program of The Real Estate Board of the Fredericton'
		. ' Area, Inc . The Detailed Information Sheet includes the name of the'
		. ' participating listing office. Information is deemed reliable but not'
		. ' guaranteed. &copy;' . date("Y") . ' Listings are property of The Real Estate Board'
		. ' of the Fredericton Area, Inc.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
