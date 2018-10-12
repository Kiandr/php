<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'Copyright &copy; ' . date("Y") . ' SEMAR MLS. The data relating to real'
		. ' estate for sale on this web site comes in part from the'
		. ' Southeast Minnesota Association of Realtors MLS.'
		. '</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The information being provided is for consumers\' personal,'
		. ' non-commercial use and may not be used for any purpose other'
		. ' than to identify prospective properties consumers may be'
		. ' interested in purchasing. Information is deemed reliable'
		. ' but is not guaranteed and should be independently verified.'
		. '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Details Pages, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
