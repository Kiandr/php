<?php

/* Get Listing */
$listing = requested_listing();

// Set cover template for enhanced listings so correct logo can be used
if ($listing['enhanced']) {
    $page->info('template', 'cover');
}

/* Require Listing */
if (!empty($listing)) {
    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE', $listing);
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS', $listing);
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION', $listing);

    // Agent subdomain
    if (Settings::getInstance()->SETTINGS['agent'] != 1) {
        // Canonical URL
        $broker_url = str_replace('http://' . Http_Host::getSubdomain() . '.', 'http://', $listing['url_details']);
        $page->info('link.canonical', $broker_url);
    }

    // List Tracking
    if (!empty($_COMPLIANCE['tracking']) && is_array($_COMPLIANCE['tracking'])) {
        IDX_COMPLIANCE::trackPageLoad($page, $listing);
    }

    // Increment Views
    $viewed = $user->info('viewed');
    $viewed = is_array($viewed) ? $viewed : array();
    $viewed_data = $user->info('viewed_data');
    $viewed_data = is_array($viewed_data) ? $viewed_data : array();
    if (!in_array($listing['ListingMLS'], $viewed)) {
        // Increment Views
        $user->incrementViews();

        // Add to Recently Viewed
        $viewed[] = $listing['ListingMLS'];
        $user->saveInfo('viewed', $viewed);

        // Save Info of Unregistered user for later import
        if (!$user->isValid()) {
            $viewed_data = $user->info('viewed_data');
            $viewed_data = is_array($viewed_data) ? $viewed_data : array();

            $viewed_data[] = array(
                'ListingMLS' => $listing['ListingMLS'],
                'table' => $idx->getTable(),
                'idx' => $idx->getName(),
                'ListingType' => $listing['ListingType'],
                'AddressCity' => $listing['AddressCity'],
                'AddressSubdivision' => $listing['AddressSubdivision'],
                'NumberOfBedrooms' => $listing['NumberOfBedrooms'],
                'NumberOfBathrooms' => $listing['NumberOfBathrooms'],
                'NumberOfSqFt' => $listing['NumberOfSqFt'],
                'ListingPrice' => $listing['ListingPrice'],
                'timestamp' => date('Y-m-d H:i:s'),
            );

            // Only Keep 20 for memory reasons otherwise crawler bots will consume a lot of resources
            if (count($viewed_data) >= 20) {
                $viewed_data = array_slice($viewed_data, -20);
            }
            $user->saveInfo('viewed_data', $viewed_data);
            if (Settings::isREW()) {
                Log::debug('viewed_data', $viewed_data);
            }
        }
    }

    // Open popup modal
    $details_popup = $user->info('details_popup');
    if (!empty($details_popup)) {
        $user->saveInfo('details_popup', false);
        $page->addJavascript('$.Window(' . json_encode(array(
            'iframe' => $details_popup
        )) . ');', 'dynamic', false); // Do not minify
    }

    /* Check Valid User */
    if ($user->isValid()) {
        /* Check Viewed Listing */
        $viewed = $db_users->fetchQuery("SELECT `id` FROM `" . TABLE_VIEWED_LISTINGS . "` WHERE "
            . "`user_id` = '" . $user->user_id() . "' AND "
            . "`mls_number` = '" . $db_users->cleanInput($listing['ListingMLS']) . "' AND "
            . "`table` = '" . $db_users->cleanInput($idx->getTable()) . "' AND"
            . "`idx` = '" . $db_users->cleanInput($idx->getName()) . "'"
        . ";");

        /* Check Viewed */
        if (!empty($viewed['id'])) {
            /* Update Times Viewed */
            $db_users->query("UPDATE `" . TABLE_VIEWED_LISTINGS . "` SET "
                           . "`views`       = `views` + 1, "
                           . "`type`        = '" . $db_users->cleanInput($listing['ListingType']) . "', "
                           . "`city`        = '" . $db_users->cleanInput($listing['AddressCity']) . "', "
                           . "`subdivision` = '" . $db_users->cleanInput($listing['AddressSubdivision']) . "', "
                           . "`bedrooms`    = '" . $db_users->cleanInput($listing['NumberOfBedrooms']) . "', "
                           . "`bathrooms`   = '" . $db_users->cleanInput($listing['NumberOfBathrooms']) . "', "
                           . "`sqft`        = '" . $db_users->cleanInput($listing['NumberOfSqFt']) . "', "
                           . "`price`       = '" . $db_users->cleanInput($listing['ListingPrice']) . "', "
                           . "`timestamp`   = NOW()"
                           . " WHERE "
                           . "`id` = '" . $viewed['id'] . "';");
        } else {
            /* Track Viewed Listing */
            $db_users->query("INSERT INTO `" . TABLE_VIEWED_LISTINGS . "` SET "
                           . "`user_id`     = '" . $user->user_id() . "', "
                           . "`mls_number`  = '" . $db_users->cleanInput($listing['ListingMLS']) . "', "
                           . "`table`       = '" . $db_users->cleanInput($idx->getTable()) . "', "
                           . "`idx`         = '" . $db_users->cleanInput($idx->getName()) . "', "
                           . "`type`        = '" . $db_users->cleanInput($listing['ListingType']) . "', "
                           . "`city`        = '" . $db_users->cleanInput($listing['AddressCity']) . "', "
                           . "`subdivision` = '" . $db_users->cleanInput($listing['AddressSubdivision']) . "', "
                           . "`bedrooms`    = '" . $db_users->cleanInput($listing['NumberOfBedrooms']) . "', "
                           . "`bathrooms`   = '" . $db_users->cleanInput($listing['NumberOfBathrooms']) . "', "
                           . "`sqft`        = '" . $db_users->cleanInput($listing['NumberOfSqFt']) . "', "
                           . "`price`       = '" . $db_users->cleanInput($listing['ListingPrice']) . "', "
                           . "`timestamp`   = NOW();");

            /* Increment Viewed Listings */
            $db_users->query("UPDATE `" . TABLE_USERS . "` SET `num_listings` = `num_listings` + 1 WHERE `id` = '" . $user->user_id() . "';");

            /* Log Event: Viewed MLS Listing */
            $event = new History_Event_Action_ViewedListing(array(
                'listing' => $listing
            ), array(
                new History_User_Lead($user->user_id())
            ));

            /* Save to DB */
            $event->save();

            // Run hook
            Hooks::hook(Hooks::HOOK_LEAD_LISTING_VIEWED)->run($user->getRow(), $idx, $listing);
        }

    // Not Logged In
    } else {
        // Set Redirect URL
        $user->setRedirectUrl($_SERVER['REQUEST_URI']);
    }

    /* Listing Images */
    if (function_exists('images')) {
        $images = images($idx, $db_idx, $listing, true);
        $listing['thumbnails'] = $images;
    }

    // Open Graph Images
    if (!empty($listing['thumbnails'])) {
        $page->info('og:image', $listing['thumbnails']);
    }

    // Get logged in user's ID
    $user_id = $user->user_id();

    // Check if listing is added to favorites
    $bookmarked = false;
    if (!empty($user_id)) {
        $bookmarked = $db_users->fetchQuery("SELECT `timestamp` FROM `users_listings` WHERE `mls_number` = '" . $db_users->cleanInput($listing['ListingMLS']) . "' AND `user_id` = '" . $db_users->cleanInput($user_id) . "' AND `agent_id` IS NULL AND `associate` IS NULL;");
    }

    // Check if listing has been dismissed
    $dismissed = false;
    if (!empty($user_id)) {
        $dismissed = $db_users->fetchQuery("SELECT `timestamp` FROM `users_listings_dismissed` WHERE `mls_number` = '" . $db_users->cleanInput($listing['ListingMLS']) . "' AND `user_id` = '" . $db_users->cleanInput($user_id) . "';");
    }

    // Listing History
    $history = \Container::getInstance()->get(\REW\Core\Interfaces\Util\IDXInterface::class)->getHistory($listing);
} else {
    // 404 Header
    header('HTTP/1.1 404 NOT FOUND');

    // Page Meta Information
    $page_title = Lang::write('IDX_DETAILS_PAGE_TITLE_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_keyw  = Lang::write('IDX_DETAILS_META_KEYWORDS_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
    $meta_desc  = Lang::write('IDX_DETAILS_META_DESCRIPTION_MISSING', array('ListingMLS' => strip_tags($_REQUEST['pid'])));
}
