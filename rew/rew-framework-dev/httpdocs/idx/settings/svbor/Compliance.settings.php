<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = 'The properties displayed are provided courtesy'
        . ' of Real Estate Brokers participating in the Sun Valley Board of'
        . ' Realtors&reg;/ Sun Valley Multiple Listing Service Internet Data'
        . ' Exchange Program (IDX).';
	$_COMPLIANCE['disclaimer'][] = '</p>';

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
    $_COMPLIANCE['disclaimer'][] = 'The IDX information is provided exclusively'
        . ' for consumers\' personal, non-commercial use. It may not be used for'
        . ' any purpose other than to identify prospective properties consumers'
        . ' may be interested in purchasing. Information provided by Sun Valley'
        . ' Board of Realtors/ Sun Valley Multiple Listing Service is deemed'
        . ' reliable but not guaranteed.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));
