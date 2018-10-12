<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Search Results - Limit
$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing information is provided by Bryan-College Station Regional MLS. </p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Page Footer Compliance
$_COMPLIANCE['footer'] = '<li><a href="http://www.trec.state.tx.us/pdf/forms/Miscellaneous/CN1-2.pdf" rel="nofollow">Texas Real Estate Commission Consumer Protection Notice</a></li><li><a href="' . Settings::getInstance()->URLS['URL_IDX'] . 'docs/IABS1-0.pdf" rel="nofollow">Information About Brokerage Services</a></li>';
