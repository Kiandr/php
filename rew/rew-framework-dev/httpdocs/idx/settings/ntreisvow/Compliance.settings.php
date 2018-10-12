<?php

// MLS Compliance
global $_COMPLIANCE;
$_COMPLIANCE = array();

// M<ust address to Terms of Use
$_COMPLIANCE['register']['agree'] = array('link' => '/terms-of-use.php', 'title' => 'Terms of Use');

// Must verify email address
$_COMPLIANCE['register']['verify'] = true;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array('');
if(in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'dashboard')) || $_REQUEST['snippet'] == true) {
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer mute">Users may not reproduce or redistribute the data found on this site. The data is for viewing purposes only. Data is deemed reliable, but is not guaranteed accurate by the MLS or NTREIS. </p>';
}

// Display Agent Name on Listing Details
$_COMPLIANCE['details']['show_agent'] = true;

// Display Office Name on Listing Details
$_COMPLIANCE['details']['show_office'] = true;