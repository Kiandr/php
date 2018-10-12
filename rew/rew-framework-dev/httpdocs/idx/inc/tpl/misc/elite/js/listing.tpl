<?php

// Converts necessary data in this listing to JSON for use elsewhere.

// Tooltip
if (empty($disableMapTooltip)) {
    ob_start();
    $listing_tooltip = $listing;
    require $page->locateTemplate('idx', 'misc', 'tooltip');
    $tooltip = str_replace(array("\r\n", "\n", "\t"), "", ob_get_clean());
} else {
    $tooltip = null;
}

// Map Options
$mapOptions = json_encode(array(
    'streetview'	=> !empty(Settings::getInstance()->MODULES['REW_IDX_STREETVIEW']),
    'center'		=> array('lat' => $listing['Latitude'], 'lng' => $listing['Longitude']),
    'manager'		=> array(
        'bounds'	=> false,
        'markers'	=> array(array(
            'tooltip'	=> $tooltip,
            'lat'		=> $listing['Latitude'],
            'lng'		=> $listing['Longitude']
        ))
    )
));

$listingOptions = json_encode(array(
    'mls' => $listing['ListingMLS'],
    'feed' => $listing['ListingFeed'] ?: Settings::getInstance()->IDX_FEED,
    'latitude' => $listing['Latitude'],
    'longitude' => $listing['Longitude']
));

$js = <<<EOF
        window.REW = window.REW || {};
        window.REW.listing = $listingOptions;
        window.REW.mapOptions = $mapOptions;
EOF;

// Add dynamic JS
$page->addJavascript($js, 'dynamic', false);
