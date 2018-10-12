<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'search_form', 'dashboard'))) {

        // Disclaimer
        $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">';
        $_COMPLIANCE['disclaimer'][] = '&copy; ' . date('Y') . ' Southwest Iowa Association of REALTORS. (SWIAR). All rights reserved. Information deemed reliable, but not guaranteed. ';
        $_COMPLIANCE['disclaimer'][] = 'Information is provided exclusively for consumers personal, non-commercial use and may not be used for any purpose other than to identify prospective properties. ';
        $_COMPLIANCE['disclaimer'][] = 'Listing information last updated on ' .  date('F jS, Y \a\t g:ia.');
        $_COMPLIANCE['disclaimer'][] = '</p>';

}

// Search Results, Display Office and Agent Name
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['show_agent'] = true;

// Listing Details, Display Office and Agent Name
$_COMPLIANCE['details']['show_office'] = true;
$_COMPLIANCE['details']['show_agent'] = true;
