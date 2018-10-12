<?php

/* IDX Compliance Settings */
$_COMPLIANCE = array();

/* Display Update Time */
$_COMPLIANCE['update_time'] = false;

/* Disclaimer Text */
$_COMPLIANCE['disclaimer'] = array();

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	/* Disclaimer */
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"><img alt="Pikes Peak Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ppmls.jpg" border="0" style="float: left; margin: 0 15px 0.5em 0;" /> The real estate listing information and related content displayed on this site is provided exclusively for consumers\' personal, non-commercial use and, may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. This information and related content is deemed reliable but is not guaranteed accurate by the Pikes Peak REALTOR&reg; Services Corp. </p>';

}

/* Search Results, Display Agent Name */
$_COMPLIANCE['results']['show_agent'] = false;

/* Search Results, Display Office Name */
$_COMPLIANCE['results']['show_office'] = false;

/* Listing Details, Display Agent Name */
$_COMPLIANCE['details']['show_agent'] = false;

/* Listing Details, Display Office Name */
$_COMPLIANCE['details']['show_office'] = (!in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local')))? true : false;

/* Brochure Logo */
$_COMPLIANCE['logo'] = Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/ppmls.jpg';
$_COMPLIANCE['logo_width'] = 35;   // Width (FPDF, Not Actual)
$_COMPLIANCE['logo_location'] = 1; // Placement

?>