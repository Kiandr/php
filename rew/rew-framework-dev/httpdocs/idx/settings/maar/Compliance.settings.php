<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Copyright&copy; ' . date("Y") . ' Memphis Area Association of REALTORS&reg;. The information provided is for the consumer\'s personal, non-commercial use and may not be used for any purpose other than to identify prospective properties that the consumer may be interested in purchasing. Information deemed reliable, but is not guaranteed accurate. Some or all of the listings displayed may not belong to the firm whose web site is being visited.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Some or all of the listings displayed may not belong to the firm whose website is being visited.</p>';

}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;
