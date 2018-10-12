<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information displayed '
		. 'on this website on listings other than those of the website owner is '
		. 'displayed with permission of the Pike Wayne Association of REALTORS '
		. 'and Broker/Participants of the Pike Wayne MLS. IDX information is '
		. 'provided exclusively for consumers’ personal, non-commercial use, '
		. 'and may not be used for any purpose other than to identify prospective '
		. 'properties consumers may be interested in purchasing. Information is '
		. 'from sources deemed reliable but is not guaranteed. Prospective buyers '
		. 'should verify information independently.</p>';
}

// Show disclaimer on dashboard
$_COMPLIANCE['dashboard']['show_disclaimer'] = true;
