<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

    // Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this web site comes in part from the Broker Reciprocity program of the FMAAR MLS. Real estate listings other than ' . $broker_name . ' are marked with the Broker Reciprocity logo and the view of detailed information about them includes the name of the listing broker. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information is being provided for consumers\' personal, noncommercial use and may not be used for any purpose other than to identify prospective properties which consumers may be interested in purchasing. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">All property listing data is Copyright ' . date("Y") . ' Fargo-Moorhead Area Association of REALTORS&reg;. No reproduction, compilation, retransmission or distribution of this data is permitted in any manner without the express, written permission of the Fargo-Moorhead Area Association of REALTORS&reg;. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information provided is not guaranteed to be accurate and should be independently verified. </p>';

	// Limited Dataset being Displayed? Then this disclaimer is required to be displayed on all IDX pages
	//$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">' . $broker_name . ' does not display the entire FMAAR MLS Broker Reciprocity database on this web site. The listings of some real estate brokerage firms have been excluded. </p>';

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map','local'));
