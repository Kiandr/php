<?php

// Global Compliance
global $_COMPLIANCE;

// IDX Compliance Settings
$_COMPLIANCE = array();

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// Disclaimer Text
$_COMPLIANCE['disclaimer']   = array();

if($_GET['load_page'] == 'details') {
	$_COMPLIANCE['disclaimer'][] = '<p class="disclaimer office"><?php global $listing; echo $listing[\'ListingOffice\'] . " " . $listing[\'ListingOfficePhoneNumber\'];?></p>';

	// This block ensures that the listing office information is not appended to the details section since we have it with the disclaimer already.
	$_COMPLIANCE['details']['extra'] = function (&$idx, &$db_idx, &$listing, &$_COMPLIANCE) {
	    return null;
	};
}

if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', 'dashboard'))) {

    $broker_name = '[BROKER NAME]';

    // Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The data relating to real estate for sale or rent on this web site comes in part from the IDX program of the HCMLS. Real estate listings held by brokerage firms other than ' . $broker_name . ' are marked with the HCIDX mark and the information about them includes the name of the listing broker.</p>';
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information provided by this website is for the personal, non-commercial use of consumers and may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing or renting.</p>';

}

// Search Results, Display MLS#
$_COMPLIANCE['results']['show_mls'] = true;

// Search Results, Display Office Name
$_COMPLIANCE['results']['show_office'] = true;

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = true;

// Listing Brochure, Remove Listing Office Text;
if (in_array($_GET['load_page'], array('brochure'))) {
    $_COMPLIANCE['details']['lang']['provider'] = '';
    $_COMPLIANCE['details']['lang']['provider_phone'] = '';
}

$_COMPLIANCE['brochure']['office']['size'] = 18;
