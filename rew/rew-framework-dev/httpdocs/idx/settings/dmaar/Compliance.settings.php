<?php

// IDX Compliance Settings
$_COMPLIANCE = array();

// Force the disclaimer on a page
$_COMPLIANCE['force_disclaimer'] = empty($_GET['load_page']);

// Disclaimer Text
$_COMPLIANCE['disclaimer'] = array('');
if (in_array($_GET['load_page'], array('', 'search', 'search_map', 'details', 'map', 'streetview', 'birdseye', 'directions', 'local', 'brochure', 'sitemap', 'dashboard'))) {
    $_COMPLIANCE['disclaimer'][] = '<p class="disclaimer">The information being provided by the Des Moines Area Association of REALTORS&reg; is exclusively for consumers\' personal, non-commercial use, and it may not be used for any purpose other than to identify prospective properties consumers may be interested in purchasing. The data is deemed reliable but is not guaranteed accurate by the MLS. </p>';
}

// Listing Details, Display Office Name
$_COMPLIANCE['details']['show_office'] = in_array($_GET['load_page'], array('details', 'brochure'));

// Custom MLS Compliance fields (Office & Agent Phone)
$_COMPLIANCE['details']['extra'] = function ($idx, $db_idx, $listing, $_COMPLIANCE) {
    return array(
        array(
            'heading' => (!empty($_COMPLIANCE['details']['lang']['listing_details']) ? $_COMPLIANCE['details']['lang']['listing_details'] : 'Listing Details'),
            'fields' => array(
                !empty($_COMPLIANCE['details']['show_agent']) ? array('title' => 'Listing Agent', 'value' => 'ListingAgent') : null,
                !empty($_COMPLIANCE['details']['show_office']) ? array('title' => (!empty($_COMPLIANCE['details']['lang']['provider']) ? $_COMPLIANCE['details']['lang']['provider'] : 'Listing Office'), 'value' => 'ListingOffice') : null,
                ($listing['HasSellersRequest'] == 'Y') ? array('block' => 'Seller is requesting showings to identified buyers only.') : null,
            ),
        ),
    );
};
