<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
        . '&copy; 2016 Lake Country GA MLS. All rights reserved. The data relating '
        . 'to real estate for sale on this web site comes in part from the '
        . 'Internet Data Exchange Program of LCGAMLS and is exclusively for '
        . 'consumers\' personal, non-commercial use, and it may not be used for '
        . 'any purpose other than to identify prospective properties consumers '
        . 'may be interested in purchasing. Information provided is deemed '
        . 'reliable but not guaranteed. Date last updated: '
        . '<?=date(\'l, F jS, Y\', strtotime($last_updated)); ?>.'
        . '</p>';

}

if (isset(Settings::getInstance()->SETTINGS['registration'])
        && !empty(Settings::getInstance()->SETTINGS['registration'])) {
    // Search Results, Display Office Name
    $_COMPLIANCE['results']['show_office'] = true;

    // Listing Details, Display Office Name
    $_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'directions', 'local'));
}
