<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	// Global Account Row
	global $account;

	// Require Broker Name
	$account['broker_name'] = !empty($account['broker_name']) ? $account['broker_name'] : '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright &copy; ' . date("Y") . ' Lubbock Association of Realtors. All rights
		reserved. All information provided by the listing agent/broker
		is deemed reliable but is not guaranteed and should be
		independently verified. Information being provided is for
		consumers' . "'" . ' personal, non-commercial use and may not be used for
		any purpose other than to identify prospective properties
		consumers may be interested in purchasing. </p>';

}
if (!in_array($_GET['load_page'], array('local', 'directions', 'map'))) {
	$_COMPLIANCE['details']['show_agent'] = true;
	$_COMPLIANCE['details']['show_office'] = true;
}

$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['show_agent'] = true;
$_COMPLIANCE['featured']['show_agent'] = true;
$_COMPLIANCE['featured']['show_office'] = true;
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;
