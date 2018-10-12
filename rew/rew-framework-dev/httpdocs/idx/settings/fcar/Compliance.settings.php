<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy;' . date("Y") . ' Flagler County'
		. ' Multiple Listing Service. All rights reserved. This data is provided'
		. ' exclusively for consumers\' personal, non-commercial use and may not'
		. ' be used for any purpose other than to identify prospective properties'
		. ' consumers may be interested in purchasing. Information deemed reliable'
		. ' but not guaranteed by the MLS.</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
