<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Information deemed reliable, but not guaranteed. Copyright Golden Empire MLS, Inc. ' . date("Y") . '. </p>';

}

$_COMPLIANCE['details']['show_agent'] = true;
$_COMPLIANCE['details']['show_office'] = true;

$_COMPLIANCE['results']['show_agent'] = true;
$_COMPLIANCE['results']['show_office'] = true;
