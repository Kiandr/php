<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided by the Coeur d\'Alene MLS is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS.</p>';

}

if (isset(Settings::getInstance()->SETTINGS['registration'])
	                && !empty(Settings::getInstance()->SETTINGS['registration'])) {
	        $_COMPLIANCE['results']['show_office'] = true;
}

if (in_array($_GET['load_page'], array('details','brochure'))) {
	        $_COMPLIANCE['details']['show_office'] = true;
}
