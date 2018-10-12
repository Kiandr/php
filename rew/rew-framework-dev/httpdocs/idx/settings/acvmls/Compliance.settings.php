<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

// The Disclaimer is only required in minimal displays if registration is
// turned on. It should be conditional. The above disclaimer is always
// required on the details and print pages.
if (in_array($_GET['load_page'], array('details', 'brochure'))
		|| (isset(Settings::getInstance()->SETTINGS['registration'])
                && !empty(Settings::getInstance()->SETTINGS['registration']))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		. 'The information being provided by the Adirondack Champlain'
		. ' Valley MLS is exclusively for consumers\' personal,'
		. ' non-commercial use, and it may not be used for any purpose'
		. ' other than to identify prospective properties consumers may'
		. ' be interested in purchasing. The data is deemed reliable'
		. ' but is not guaranteed accurate by the MLS.'
		. '</p>';

}

// The listing office is only required in minimal displays if registration
// is turned on. It should be conditional.
if (isset(Settings::getInstance()->SETTINGS['registration'])
		&& !empty(Settings::getInstance()->SETTINGS['registration'])) {
	$_COMPLIANCE['results']['show_office'] = true;
}

// The listing office is always required on the details and print pages.
$_COMPLIANCE['details']['show_office'] = true;
