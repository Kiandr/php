<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">'
		.'<img alt="Tucson Association of Realtors Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG']
		. 'logos/TARMLS-IDX-Icon.png" alt="TARMLS Logo" border="0"'
		.' style="float: left; margin-right: 2px; " />'
		.'The information being provided by the Tucson Association of REALTORS&reg; exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';

}

if (isset(Settings::getInstance()->SETTINGS['registration'])
		&& !empty(Settings::getInstance()->SETTINGS['registration'])) {
	// Search Results, Display Office Name
	$_COMPLIANCE['results']['show_office'] = true;
}
