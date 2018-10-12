<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only Show on Certain Pages
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer Text
	$_COMPLIANCE['disclaimer'][] = '<p>All or a portion of the multiple Listing information is provided by the Central Virginia Regional Multiple Listing Service, LLC, from a copyrighted compilation of Listings. All CVR MLS information provided is deemed reliable but is not guaranteed accurate. The compilation of Listings and each individual Listing are &copy;'.date('Y').' Central Virginia Regional Multiple Listing Service, LLC. All rights reserved. </p>';

}

// Listing Details: Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));
