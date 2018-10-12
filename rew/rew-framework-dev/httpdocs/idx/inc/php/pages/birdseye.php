<?php

// Birds Eye View Not Enabled, Re-Direct
if (empty(Settings::getInstance()->MODULES['REW_IDX_BIRDSEYE'])) {
    header('Location: /');
    exit;
}

// Get Listing
$listing = requested_listing();

// Require Listing
if (!empty($listing)) {
    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_BIRDSEYE_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_BIRDSEYE_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_BIRDSEYE_META_DESCRIPTION', $listing);

    // Map points
    $points = !empty($listing['Latitude']) && !empty($listing['Longitude']);
    if (!empty($points)) {
        // Require map javascript
        $page->getSkin()->loadMapApi();
    }

    // List Tracking
    if (!empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
        IDX_COMPLIANCE::trackPageLoad($page, $listing);
    }
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
