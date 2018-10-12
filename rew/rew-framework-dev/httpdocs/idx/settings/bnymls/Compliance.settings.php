<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Require Broker Name
	$broker_name = '[INSERT BROKER NAME]';

	if (in_array($_GET['load_page'], array('search_map','search', 'map', 'directions', 'local'))) {
		// Search Results: Display Office Name
		$_COMPLIANCE['results']['lang']['provider'] = 'Listing courtesy of ';
		$_COMPLIANCE['results']['show_office'] = true;
	} else {
		$_COMPLIANCE['disclaimer'][] = '<p>Listing courtesy of <?=$listing[\'ListingOffice\'];?></p>' . PHP_EOL . PHP_EOL;
	}

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '&copy; <?=date(\'Y\'); ?> Brooklyn New York MLS. All rights reserved. IDX information is provided exclusively for consumers\' personal, non-commercial use and may not be used for any purpose '
		. 'other than to identify prospective properties consumers may be interested in purchasing. Information is deemed reliable but is not guaranteed accurate by the MLS or ' . $broker_name . '.'
		. 'Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>';
	$_COMPLIANCE['disclaimer'][] = '</p>';

}
