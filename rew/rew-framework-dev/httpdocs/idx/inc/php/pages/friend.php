<?php

// Get Requested Listing
$listing = requested_listing();

// Require Listing
if (!empty($listing)) {
    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_SHARE_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_SHARE_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_SHARE_META_DESCRIPTION', $listing);

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
