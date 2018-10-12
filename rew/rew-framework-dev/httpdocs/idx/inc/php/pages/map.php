<?php

// Get Requested Listing
$listing = requested_listing();

// IDX Mapping Not Enabled, Re-Direct
if (empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
    header("Location: " . $listing['url_details'], true, 301);
    exit;
}

// Require Listing Row
if (!empty($listing)) {
    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_MAP_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_MAP_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_MAP_META_DESCRIPTION', $listing);

    // Center Co-Ords
    $center = array();

    // Center Map
    $points = !empty($listing['Latitude']) && !empty($listing['Longitude']);
    if (!empty($points)) {
        $center['latitude']  = $listing['Latitude'];
        $center['longitude'] = $listing['Longitude'];
    }

    // Google Directions Enabled
    if (empty($center) && !empty(Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'])) {
        $center['latitude']  = Settings::getInstance()->SETTINGS['map_latitude'];
        $center['longitude'] = Settings::getInstance()->SETTINGS['map_longitude'];
    }

    // Map points
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
