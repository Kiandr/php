<?php

// MLS Compliance
global $_COMPLIANCE;

// Limit (Default: 3)
$limit = isset($this->config['limit']) ? $this->config['limit'] : 3;

// View Class (Default: flowgrid)
$viewClass = isset($this->config['viewClass']) ? $this->config['viewClass'] : 'flowgrid';

// Thumbnail Size
$thumbnails = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : false;

// Placeholder Image
$placeholder = !empty($this->config['placeholder']) ? $this->config['placeholder'] : '/img/blank.gif';
$placeholder = !empty($thumbnails) ? '/thumbs/' . $thumbnails . $placeholder : $placeholder;

// Cache Duration In Seconds (Default: 0)
$cache = isset($this->config['cache']) ? $this->config['cache'] : 0;

// CMS Database
$db = DB::get('cms');

// Listing Results
$results = array();

try {
    // Load saved favorites
    $idx = Util_IDX::getIdx();
    if (empty($idx)) {
        return;
    }
    $user = User_Session::get();
    $bookmarked = $user->getSavedListings($idx);

    // Cache Index
    $index = $this->getContainer()->getID() . ':' . $this->getUID();

    // Check Memcache
    $cached = !empty($cache) ? Cache::getCache($index) : null;
    if (!is_null($cached)) {
        // Use Cache
        $results = $cached;
    } else {
        // Select from installed feeds
        $feeds = [Settings::getInstance()->IDX_FEED];
        if (!empty(Settings::getInstance()->IDX_FEEDS)) {
            $feeds = array_keys(Settings::getInstance()->IDX_FEEDS);
        }

        // Featured Listings
        $query = $db->prepare("SELECT * FROM `featured_listings` WHERE `idx` IN (" . implode(', ', array_fill(0, count($feeds), '?')) . ") ORDER BY RAND();");
        $query->execute($feeds);
        $featured = $query->fetchAll();

        // Process Featured Listings
        foreach ($featured as $listing) {
            try {
                // IDX Feed
                $idx = Util_IDX::getIdx($listing['idx']);

                // IDX Database
                $db_idx = Util_IDX::getDatabase($listing['idx']);

                // Generate WHERE clause
                $sql_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($listing['mls_number']) . "' ";

                // Filter only broker's listings
                if (!empty($_COMPLIANCE['featured']['broker']) && $this->config['mode'] == 'featured') {
                    $sql_where .= " AND " . $idx->field('ListingOfficeID') . " LIKE '" . $db_idx->cleanInput($_COMPLIANCE['featured']['broker']) . "'";
                }

                // Any global criteria
                $idx->executeSearchWhereCallback($sql_where);

                // IDX Listing
                $result = $db_idx->fetchQuery("SELECT SQL_CACHE " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $sql_where . ";");

                // Require Listing
                if (!empty($result)) {
                    // Featured Over-Ride Details
                    if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS_OVERRIDE'])) {
                        $result['AddressCity']       = !empty($listing['city'])       ? $listing['city']      : $result['AddressCity'];
                        $result['ListingPrice']      = !empty($listing['price'])      ? $listing['price']     : $result['ListingPrice'];
                        $result['NumberOfBedrooms']  = !empty($listing['bedrooms'])   ? $listing['bedrooms']  : $result['NumberOfBedrooms'];
                        $result['NumberOfBathrooms'] = !empty($listing['bathrooms'])  ? $listing['bathrooms'] : $result['NumberOfBathrooms'];
                        $result['ListingRemarks']    = !empty($listing['remarks'])    ? $listing['remarks']   : $result['ListingRemarks'];
                        if (!empty($listing['image']) && file_exists(DIR_FEATURED_IMAGES . $listing['image'])) {
                            $result['ListingImage'] = URL_FEATURED_IMAGES . $listing['image'];
                        }
                    }

                    // Result IDX
                    $result['idx'] = $idx->getLink();

                    // Parse Result
                    $result = Util_IDX::parseListing($idx, $db_idx, $result);

                    // Thumbnail URL
                    if (!empty($thumbnails)) {
                        $result['ListingImage'] = Format::thumbUrl($result['ListingImage'], $thumbnails);
                    }

                    // Add to Results
                    $results[] = $result;

                    // Check Limit, Stop If Reached
                    if (!empty($limit) && count($results) >= $limit) {
                        break;
                    }
                }

            // Error Occurred
            } catch (Exception $e) {
                Log::error($e);
            }
        }

        // Save Cache
        if (!empty($cache)) {
            Cache::setCache($index, $results, false, $cache);
        }
    }

    // Set Snippet
    if (!empty($results)) {
        $_REQUEST['snippet'] = true;
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
