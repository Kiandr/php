<?php

// MLS Compliance Settings
global $_COMPLIANCE;
$_COMPLIANCE = array();

// Search Result Limit
$_COMPLIANCE['limit'] = 1500;

// Display Update Time
$_COMPLIANCE['update_time'] = true;

// MLS Disclaimer
$_COMPLIANCE['disclaimer'] = array();

// Only Show on Certain PAges
if (in_array($_GET['load_page'], array('search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'local', 'brochure', 'sitemap', '', 'dashboard'))) {

    // MLS Disclaimer
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">Disclaimer: Information herein deemed reliable but not guaranteed by the EREB.</p>';

}
// Image rules
$_COMPLIANCE['images']['no_overlay'] = true;

// Remove anything that differentiates reduced listings from regular listings
$_COMPLIANCE['flags']['hide_price_reduction'] = true;

// Search Results Compliance
$_COMPLIANCE['results']['show_mls'] = true;
$_COMPLIANCE['results']['show_office'] = true;
$_COMPLIANCE['results']['fav_first'] = true;
$_COMPLIANCE['results']['provider_first'] = function($listing_result) { return stripos($listing_result['ListingMLS'], 'e') === 0; };

// Details Page Compliance
$_COMPLIANCE['details']['show_office'] =  true;
$_COMPLIANCE['details']['provider_first'] = function($listing_result) { return stripos($listing_result['ListingMLS'], 'e') === 0; };

// Strip Certain Words from Meta Information
$_COMPLIANCE['strip_words']['meta_description'] = array('realtors', 'realtor', 'mls®', 'mls', 'multiple listings service');

// Show provider first in details section
$_COMPLIANCE['details']['details_provider_first'] = true;
