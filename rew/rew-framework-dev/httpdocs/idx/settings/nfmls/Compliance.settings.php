<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating'
		.' to real estate for sale on this web site comes in part from the'
		.' Internet Data Exchange of the Lake City Board of Realtors. The'
		.' information being provided is for consumers personal,'
		.' non-commercial use and may not be used for any purpose other'
		.' than to identify prospective properties consumers may be'
		.' interested in purchasing. Information deemed reliable but not'
		.' guaranteed. Copyright&copy; ' . date("Y") . ' Lake City Board of Realtors.'
		.' All rights reserved.</p>';

}

$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['results']['show_office'] = true;
