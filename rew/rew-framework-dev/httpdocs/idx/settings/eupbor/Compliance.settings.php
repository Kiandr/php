<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'Listing information is provided in part by the IDX Program of the MLS of the Eastern Upper Peninsula Board of REALTORS&reg;. '
		. 'All information is deemed reliable but is not guaranteed. Listing information last updated on '
		. date('F jS, Y \a\t h:ia, T')
		. '</p>';

}

if (in_array($_GET['load_page'], array('details', 'brochure'))) {
        $_COMPLIANCE['details']['show_office'] = true;
}

