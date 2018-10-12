<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">'
		.'The information being provided by the Central Georgia MLS is exclusively'
		.' for consumers\' personal, non-commercial use, and it may not be used for'
		.' any purpose other than to identify prospective properties consumers may'
		.' be interested in purchasing. The data is deemed reliable but is not'
		.' guaranteed accurate by the MLS.'
		.'</p>';

}

$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map','local'));
$_COMPLIANCE['results']['show_office'] = true;
