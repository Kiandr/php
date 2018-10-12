<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

$_COMPLIANCE['limit'] = 100;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '&copy; ' . date('Y') . ' Quad City Area Realtor Association. All rights reserved. This data is provided exclusively for consumers personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information deemed reliable but not guaranteed. Listing broker has attempted to offer accurate data, but buyers are advised to confirm all items.';

	$_COMPLIANCE['disclaimer'][] = '</p>';
}

$_COMPLIANCE['details']['show_agent'] = true;
$_COMPLIANCE['details']['show_office'] = true;
