<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">IDX is deemed reliable but is not guaranteed accurate by the Hawaii Information Service, Inc. Listing Service. &copy; ' . date('Y') . ' Hawaii Information Service. All Rights Reserved.</p>';

}

$_COMPLIANCE['results']['show_office'] = true;

$_COMPLIANCE['details']['show_office'] = !in_array($_GET['load_page'], array('map', 'local'));
