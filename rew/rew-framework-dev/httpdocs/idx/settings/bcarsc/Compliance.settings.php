<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

    $_COMPLIANCE['disclaimer'][] = '<div style="text-align: center;">';
    $_COMPLIANCE['disclaimer'][] = '<p>Data last updated: <?=date(\'F jS, Y \a\t g:ia T\', strtotime($last_updated)); ?> and updating occurs every 15 minutes</p>';
    $_COMPLIANCE['disclaimer'][] = '<p>The information being provided by BEAUFORT&reg; is for consumers\' personal, non-commercial use and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
    $_COMPLIANCE['disclaimer'][] = '</div>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('map', 'streetview', 'birdseye', 'local'))) ? false : true;
