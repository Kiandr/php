<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Required if Forced Registration
	if (isset(Settings::getInstance()->SETTINGS['registration']) && !empty(Settings::getInstance()->SETTINGS['registration'])) {

		$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'directions');
		$_COMPLIANCE['disclaimer'][] = '<p>The information being provided by the Sanibel & Captiva Islands Association of Realtors&reg;, Inc. is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';

	} else if (in_array($_GET['load_page'], array('details', 'brochure'))) {

		$_COMPLIANCE['details']['show_office'] = true;
		$_COMPLIANCE['disclaimer'][] = '<p>The information being provided by the Sanibel & Captiva Islands Association of Realtors&reg;, Inc. is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
	}

}

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
