<?php
use REW\Core\Interfaces\SettingsInterface;
use REW\Module\EnhancedListings\Store\EnhancedListingStore;

// Get page reference
$page = $this->getContainer()->getPage();

// Get site feeds
$feeds = Settings::getInstance()->IDX_FEEDS;

// Work around since cms is not and IDX feed, therefore not search by quick search
$feeds['cms']['title'] = "Pocket Listings";

$_COOKIE['enhanced-feed'] = $_COOKIE['enhanced-feed'] ?: Settings::getInstance()->IDX_FEED;

$user = User_Session::get();
$user->saveInfo($_COOKIE['enhanced-feed'],  $_REQUEST['p']);

// Enhanced listing store
$container = \Container::getInstance();
$enhancedListingStore = $container->make(EnhancedListingStore::class);

// Find enhanced listings
$enhancedListings = [];
$mlsFeeds = [];
foreach ($enhancedListingStore->getEnhancedListings() as $enhancedListing) {
    $feed = $enhancedListing['mls_feed'];
    $mlsFeeds[$feed] = $feeds[$feed];
    $mlsNumber = $enhancedListing['mls_number'];
    $enhancedListings[$feed][] = $mlsNumber;
}

// Current IDX feed
$settings = $container->get(SettingsInterface::class);

// Set IDX snippet criteria
$_REQUEST['snippet'] = true;
$tab_content = [];

foreach ($mlsFeeds as $feed => $content) {

    // If the feed is already loaded from the cache
    if($useCache && $tab_content[$feed]['cached']) continue;

    // Search by MLS Numbers
    $_REQUEST['search_mls'] = $enhancedListings[$feed];
    $_REQUEST['search_title'] = "Enhanced Listings for " . $mlsFeeds[$feed]['title'];
    $_REQUEST['p'] = $user->info($feed) ?: 1;

    $enhanced_search = $page->load('idx', 'search', $feed);
    $enhanced_search['feed'] = $feed;
    $tab_content[$feed] = $enhanced_search;

    // Clear IDX snippet criteria
    unset($_REQUEST['search_mls']);

    if (!isset($mlsFeeds)) { ?>
        <div class="notice">
            <div class="notice__message">
                No listings were found matching your search criteria.
            </div>
        </div>
<?php 
    }
}

unset($_REQUEST['snippet']);

