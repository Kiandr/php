<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

$_COMPLIANCE['limit'] = 500;
$_REQUEST['page_limit'] = 25;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form','dashboard'))) {

	$broker_name = '[INSERT BROKER NAME]';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = '<img alt="Colorado Real Estate Network Logo" src="' . Settings::getInstance()->SETTINGS['URL_IMG'] . 'logos/cren.jpg" border="0" />';
	$_COMPLIANCE['disclaimer'][] = 'The data relating to real estate for sale on this web site comes in part from the Internet Data Exchange (IDX) program of Colorado Real Estate';
	$_COMPLIANCE['disclaimer'][] = ' Network, Inc. (CREN), &copy; Copyright ' . date("Y") . '. All rights reserved. All data deemed reliable but not guaranteed and should be independently verified.';
	$_COMPLIANCE['disclaimer'][] = ' This database record is provided subject to "limited license" rights. Duplication or reproduction is prohibited. <br />';
	$_COMPLIANCE['disclaimer'][] = ' <a href="http://www.crenmls.com/full-cren-disclaimer-2/" >FULL CREN Disclaimer</a>';
	$_COMPLIANCE['disclaimer'][] = '</p>';

	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
	$_COMPLIANCE['disclaimer'][] = 'Real Estate listings held by companies other than ' . $broker_name . ' contain that company\'s name. <br />';
	$_COMPLIANCE['disclaimer'][] = ' <a href="http://www.crenmls.com/wp-content/uploads/2014/02/Fair-Housing-Disclaimer2.pdf">Fair Housing Disclaimer</a>';
	$_COMPLIANCE['disclaimer'][] = '</p>';
}

$_COMPLIANCE['details']['show_office'] = true;

