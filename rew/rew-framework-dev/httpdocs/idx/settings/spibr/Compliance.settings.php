<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Listing information'
		. ' provided by South Padre Island Board of REALTORS MLS.  IDX Information'
		. ' is provided exclusively for consumers\' personal, non-commercial use'
		. ' and may not be used for any purpose other than to identify prospective'
		. ' properties consumers may be interested in purchasing. Data is deemed'
		. ' reliable but not guaranteed accurate by the MLS.    Date of last'
		. ' update <?=date(\'n / j / Y\', strtotime($last_updated)); ?>.</p>';

}

if (in_array($_GET['load_page'], array('details', 'brochure'))) {

	// Listing Details, Display Agent
	$_COMPLIANCE['details']['show_agent'] = true;

	// Listing Details, Display Office
	$_COMPLIANCE['details']['show_office'] = true;
}
