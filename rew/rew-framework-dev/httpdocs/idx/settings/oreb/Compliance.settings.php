<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Global Account Row
	global $account;

	// Require Broker Name
	$account['broker_name'] = !empty($account['broker_name']) ? $account['broker_name'] : 'Keller Williams Ottawa Realty';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"></p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details', 'brochure', 'birdseye', 'streetview')) ? true : false);
