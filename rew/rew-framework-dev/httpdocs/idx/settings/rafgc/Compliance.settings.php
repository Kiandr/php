<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Property information provided by the REALTOR\'S&reg; '.
		'Association of Franklin and Gulf Counties, Inc. IDX information is provided exclusively for consumers\' personal,'.
		' non-commercial use, and may not be used for any purpose other than to identify prospective properties consumers'.
		' may be interested in purchasing. This data is deemed reliable but is not guaranteed accurate by the MLS.</p>';
}
