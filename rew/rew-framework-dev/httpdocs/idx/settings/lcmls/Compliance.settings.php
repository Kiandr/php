<?php

/* IDX Compliance Settings */
$_COMPLIANCE = array();

/* Display Update Time */
$_COMPLIANCE['update_time'] = false;

$_COMPLIANCE['limit'] = 200;

/* Disclaimer Text */
$_COMPLIANCE['disclaimer']   = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	/* Disclaimer */
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided by the Lincoln County Multiple Listing Service is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';

}

/* Search Results, Display Thumbnail Icon */
$_COMPLIANCE['results']['show_icon'] = false;

/* Search Results, Display Agent Name */
$_COMPLIANCE['results']['show_agent'] = false;

/* Search Results, Display Office Name */
$_COMPLIANCE['results']['show_office'] = false;

/* Listing Details, Display Agent Name */
$_COMPLIANCE['details']['show_agent'] = true;

/* Listing Details, Display Office Name */
$_COMPLIANCE['details']['show_office'] = true;

?>