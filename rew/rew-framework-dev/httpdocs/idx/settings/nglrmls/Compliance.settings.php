<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The accuracy of all information, regardless of source, is not guaranteed or warranted. All information should be independently verified. Copyright &copy; Northern Great Lakes REALTORS&reg; MLS. All Rights Reserved.'
		. '</p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name and Phone Number
$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['details']['show_office_phone'] = true;
