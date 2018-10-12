<?php

// Streetview Not Enabled, Re-Direct
if (empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'])) {
    header('Location: /');
    exit;
}

// Get Listing
$listing = requested_listing();

// Require Listing
if (!empty($listing)) {
    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_STREETVIEW_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_STREETVIEW_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_STREETVIEW_META_DESCRIPTION', $listing);

    // Map points
    $points = !empty($listing['Latitude']) && !empty($listing['Longitude']);
    if (!empty($points)) {
        // Skins using Grunt can ignore the JS addition
        if (!in_array(Settings::getInstance()->SKIN, array('elite'))) {
            // Require map javascript
            $page->getSkin()->loadMapApi();

            // Add streetview javascript (let's skip minify)
            $page->addJavascript('(function () {
				new REWMap.Streetview({
					el: document.getElementById(\'streetview-container\'),
					lat: ' . floatval($listing['Latitude']) . ',
					lng: ' . floatval($listing['Longitude']) . ',
					onFailure: function () {
						this.opts.el.innerHTML = "<p class=\"msg negative\">We\'re sorry, but Google Streetview is currently unavailable for this property.</p>";
						this.opts.el.style.height = "auto";
					}
				});
			})();', 'dynamic', false);
        }
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
