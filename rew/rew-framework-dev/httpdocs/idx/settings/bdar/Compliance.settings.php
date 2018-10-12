<?php
// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">These Properties are'
		. ' provided courtesy of Broker Reciprocity/IDX Lake Of The Ozarks Board'
		. ' of REALTORS&reg; and Bagnell Dam Association of REALTORS&reg; MLS, Inc.'
		. ' Multiple Listing Service. This information is copyrighted by The'
		. ' Lake Of The Ozarks Board of REALTORS&reg; and The Bagnell Dam Association'
		. ' of REALTORS&reg; MLS, Inc. Multiple Listing Service. Information is being'
		. ' provided for consumers\' personal, non-commercial use and may not be'
		. ' used for any purpose other than to identify prospective properties'
		. ' consumers may be interested in purchasing. All information deemed'
		. ' reliable but not guaranteed and should be independently verified.'
		. ' All properties are subject to prior sale, change, or withdrawal.'
		. ' Information herein deemed reliable but not guaranteed. Copyright 2014'
		. ' Lake Of The Ozarks Board of Realtors, Bagnell Dam Assoc. of Realtors.'
		. ' Last Updated: <?=date(\'F j, Y g:i a\', strtotime($last_updated)); ?></p>';

}

// Dashboard, Show Disclaimer
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;

// The listing office, preceded by the words "Listing courtesy of" is required
$_COMPLIANCE['results']['lang']['provider'] = 'Listing Courtesy of ';
$_COMPLIANCE['results']['show_office'] = true;

// Show Office on Details and Brochure page
if (!in_array($_GET['load_page'], array('map', 'local'))) {
	$_COMPLIANCE['details']['lang']['provider'] = 'Listing Courtesy of ';
	$_COMPLIANCE['details']['show_office'] = true;
}
