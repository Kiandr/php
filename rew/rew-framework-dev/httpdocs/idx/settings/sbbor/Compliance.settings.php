<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['limit'] = 200;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
	$_COMPLIANCE['disclaimer'][] = "<p class='disclaimer'>Information is deemed reliable but not guaranteed.<br />Copyright <?=date('Y');?>, Participating Associations in the SEFMLS. All rights reserved.</p>";
	$_COMPLIANCE['disclaimer'][] = "<p class='disclaimer'>This information being provided is for consumer's personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>";
	$_COMPLIANCE['disclaimer'][] = "<p class='disclaimer'>Use of data on this site, other than by a consumer looking to purchase real estate, is prohibited.</p>";

}

if (!in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'directions', 'local'))) {
	// Listing Details, Display Office Name
	$_COMPLIANCE['details']['show_office'] = true;
}

?>
