<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$dre_number = 'SCAOR 08098'; // CHANGE ME

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Based on information from Sussex County Association of REALTORS&reg;, Inc which neither guarantees nor is in any way responsible for it\'s accuracy. All data is provided "AS IS" and with all it\'s faults. Data maintained by Sussex County Association of REALTORS&reg;, Inc. may not reflect all real estate activity in the market Bay Coast Realty is a Real Estate licensee in the State of Delaware. SCAOR DE License Number: <strong>'.$dre_number.'</strong>. </p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright ' . date('Y') . ' Sussex County Association of REALTORS&reg;, Inc. </p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'));
