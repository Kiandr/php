<?php

global $_COMPLIANCE;

// Listing record
$similar = array();
$listing = $this->config('listing');
if (!empty($listing)) {
    $similar = array();

    // Load IDX and Database
    $idx = Util_IDX::getIDX($listing['idx']);
    $db_idx = Util_IDX::getDatabase($listing['idx']);

    // Similar listings are:
    // - of the same listing status
    // - of the same property type
    $sql_where = array();
    foreach (array('ListingType', 'ListingSubType', 'ListingStatus') as $match) {
        $field = $idx->field($match);
        if (!empty($field)) {
            $value = (string) $listing[$field];
            if (!empty($value)) {
                $value = $db_idx->cleanInput($value);
                $sql_where[] = "`" . $field . "` = '" . $value . "'";
            }
        }
    }

    // Match by zip code (or postal code)
    $field = $idx->field('AddressZipCode');
    if (!empty($field)) {
        $value = (string) $listing[$field];
        if (!empty($value)) {
            // Canadian site - match first 3 characters of postal code
            if (Settings::getInstance()->LANG === 'en-CA') {
                $value = $db_idx->cleanInput(substr($value, 0, 3));
                $sql_where[] = "`" . $field . "` LIKE '" . $value . "%'";
            } else {
                $value = $db_idx->cleanInput($value);
                $sql_where[] = "`" . $field . "` = '" . $value . "'";
            }
        }
    }

    // Load dismissed listings
    $user = User_Session::get();
    $dismissed = $user->getDismissedListings($idx);

    // Exclude dismissed listings
    if (!empty($dismissed)) {
        $sql_where[] = "`ListingMLS` NOT IN ('" . implode("', '", $dismissed) . "')";
    }

    // Not the same listing
    $sql_where[] = "`ListingMLS` != '" . $db_idx->cleanInput($listing['ListingMLS']) . "'";

    // Priced 25% more/less of listing
    $price = $listing['ListingPrice'];
    $min_price = $price - ($price * 0.25);
    $max_price = $price + ($price * 0.25);
    $sql_where[] = "(`ListingPrice` BETWEEN " . (int) $min_price . " AND " . (int) $max_price . ")";

    // Any global criteria
    $idx->executeSearchWhereCallback($sql_where);

    // Generate database query
    $queryString = "SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "`"
        . " WHERE " . implode(' AND ', $sql_where)
        . " ORDER BY `id` DESC LIMIT " . ((int) $this->config('limit') ?: 3)
    ;

    // Check if results are already cached
    $cacheIndex = md5($queryString);
    $cacheData = Cache::getCache($cacheIndex);
    if (!is_null($cacheData)) {
        $similar = $cacheData;
    } else {
        // Fetch similar listings
        if ($result = $db_idx->query($queryString)) {
            while ($similar_listing = $db_idx->fetchArray($result)) {
                $similar[] = Util_IDX::parseListing($idx, $db_idx, $similar_listing);
            }
        }

        // Save results to cache
        Cache::setCache($cacheIndex, $similar);
    }

    // Similar listings found
    if (!empty($similar)) {
        // Locate search result template
        $page = $this->getContainer()->getPage();
        $result_tpl = $page->locateTemplate('idx', 'misc', 'result');

        // Load saved favorites
        $bookmarked = $user->getSavedListings($idx);
    }
}
