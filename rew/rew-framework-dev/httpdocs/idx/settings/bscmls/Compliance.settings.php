<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

/* Disclaimer Text */
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

	// Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer"> The information provided by this website is for the personal, non-commercial use of consumers and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. All information is deemed reliable; however, it is not guaranteed and should be independently verified. Real Estate offerings are subject to error, omissions, prior sale, change, withdrawal without notice and approval of seller. Properties are provided courtesy of Big Sky Country MLS. </p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));
