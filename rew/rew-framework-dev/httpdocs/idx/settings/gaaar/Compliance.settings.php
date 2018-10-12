<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

    // Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX information is provided exclusively for consumers\' personal, non-commercial use, and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this website comes in part from the GAAAR IDX Program.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing data is deemed reliable but is not guaranteed accurate by the MLS.</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details','brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details','brochure'));
