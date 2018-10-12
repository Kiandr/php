<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The information being provided by the Northwest Iowa Regional Board of '
		. 'REALTORS&reg; is exclusively for consumers\' personal, non-commercial use, '
		. 'and it may not be used for any purpose other than to identify '
		. 'prospective properties consumers may be interested in purchasing. The '
		. 'data is deemed reliable but is not guaranteed accurate by the MLS.'
		. '</p>';

}

// Display Agent and Office if Registration on
if (isset(Settings::getInstance()->SETTINGS['registration'])
		&& !empty(Settings::getInstance()->SETTINGS['registration'])) {
	$_COMPLIANCE['results']['show_office'] = true;
	$_COMPLIANCE['results']['show_agent'] = true;
}
