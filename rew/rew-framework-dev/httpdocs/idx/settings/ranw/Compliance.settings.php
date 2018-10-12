<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Global Account Row
	global $account;

	// Require Broker Name
	$account['broker_name'] = !empty($account['broker_name']) ?
		$account['broker_name'] : '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information received'
		.' from other 3rd parties: All information deemed reliable but not'
		.' guaranteed and should be independently verified. All properties'
		.' are subject to prior sale, change, or withdrawal. Neither listing'
		.' broker nor ' . $account['broker_name'] . ' nor RANW MLS shall be'
		.' responsible for any typographical errors, misinformation, misprints,'
		.' and shall be held totally harmless.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright <?=date(\'Y\');?> -'
		.' REALTORS&reg; Association of Northeast Wisconsin Multiple Listing'
		.' Service - All Rights Reserved.</p>';

}

$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['results']['show_office'] = true;
