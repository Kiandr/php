<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">The Association of Regina REALTORS&reg; Inc. (ARR) IDX listings are displayed in accordance with ARR\'s ' . Lang::write('MLS') . ' Data Access Agreement and are copyright of the Association of Regina REALTORS&reg; Inc.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">The above information is from sources deemed reliable but should not be relied upon without independent verification. The information presented here is for general interest only, no guarantees apply.</p>';
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">' . Lang::write('MLS') . ' System data of the Association of Regina REALTORS&reg; Inc. displayed on this site is refreshed every 15 minutes.</p>';
}

// Limit search results
$_COMPLIANCE['limit'] = 200;

// Show Office
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['details']['show_office'] = true;
