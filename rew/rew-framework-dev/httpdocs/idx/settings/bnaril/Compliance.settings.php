<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Broker name
$broker_name = '[INSERT BROKER NAME]';

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
	if (in_array($_GET['load_page'], array('details', 'brochure'))) {
		$_COMPLIANCE['disclaimer'][] = '<p>Listing courtesy of <?=$listing[\'ListingOffice\'];?> of <?=$listing[\'ListingAgent\'];?></p>' . PHP_EOL . PHP_EOL;
	}
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '&copy; <?=date(\'Y\');?> Bloomington-Normal Association of Realtors&reg;, Inc. All rights reserved. IDX information is provided exclusively for consumers\' personal, non-commercial use '
		. 'and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. Information is deemed reliable but is not guaranteed accurate by the MLS '
		. 'or ' . $broker_name . '. Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?>.';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

?>
