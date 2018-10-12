<?php

// IDX Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');

// Only Include on Certain Pages
if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Users may not reproduce or redistribute the data found on this site. The data is for viewing purposes only. Data is deemed reliable, but is not guaranteed accurate by the MLS or NTREIS.</p>';

}

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = (in_array($_GET['load_page'], array('details', 'brochure'))) ? true : false;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = (in_array($_GET['load_page'], array('details', 'brochure'))) ? true : false;

?>