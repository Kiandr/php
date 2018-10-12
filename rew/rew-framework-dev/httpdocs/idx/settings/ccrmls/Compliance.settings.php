<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The Data relating to'
		. ' real estate for sale on this web site comes in part from the Internet'
		. ' Data Exchange (IDX) program of the Central Coast Multiple Listing'
		. ' Service and its affiliated associations.  This data is provided'
		. ' exclusively for consumers\' personal, non-commercial use and may not'
		. ' be used for any purpose other than to identify prospective properties'
		. ' consumers may be interested in purchasing. Information deemed reliable'
		. ' but not guaranteed. Listing broker has attempted to offer accurate'
		. ' data, but buyers are advised to confirm all items.  &copy; ' . date('Y')
		. ' by the following Associations of REALTORS&reg;: Atascadero, Lompoc'
		. ' Valley, Paso Robles, Scenic Coast, San Luis Obispo, Pismo Coast,'
		. ' Santa Maria and Santa Ynez Valley. All Rights Reserved.'
		. ' <?=date(\'F j, Y g:i a\', strtotime($last_updated)); ?></p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Dashboard, Display Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

