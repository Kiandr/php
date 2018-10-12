<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array('');

if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', '', 'sitemap', 'dashboard'))) {

	// Require Broker Name
    $broker_name = '[INSERT BROKER NAME]';

    // Disclaimer
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">&copy; ' . date("Y") . ' TREND, All Rights Reserved. Information Deemed Reliable But Not Guaranteed.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale on this website appears in part through the TREND Internet Data Exchange program, a voluntary cooperative exchange of property listing data between licensed real estate brokerage firms in which ' . $broker_name . ' participates, and is provided by TREND through a licensing agreement.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information provided by this website is for the personal, non-commercial use of consumers and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Some properties which appear for sale on this website may no longer be available because they are under contract, have sold or are no longer being offered for sale.</p> ';

}

// Search Results, Display Agent Name
$_COMPLIANCE['results']['show_agent'] = false;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Agent Name
$_COMPLIANCE['details']['show_agent'] = false;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = ($_GET['load_page'] != 'map' ? true : false);


?>