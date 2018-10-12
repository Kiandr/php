<?php

namespace BDX;

try {
    // Initialize Results & Tracker Array
    $results = array();
    $BDXTracker = array();
    
    // Initialize Breadcrumb Array
    $breadcrumbOptions = array();
    
    // Load Listing from DB
    $result = $app->db_bdx->prepare("SELECT `Listing`.*, `Subdivision`.`SubdivisionName`, `Subdivision`.`State`, `Subdivision`.`City`, `Subdivision`.`BrandName` "
                                    . "FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing` "
                                    . "LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID` "
                                    . "WHERE `Listing`.`SubdivisionID` = :SubdivisionID AND `ListingID` = :ListingID;");

    $result->execute(array('SubdivisionID' => $community, 'ListingID' => $listing));
    $listing = $result->fetch();
    
    if (!empty($listing)) {
        // Parse Listing
        $listing = Listing::parse($listing, $app);
        
        // Add to BDX tracker
        $BDXTracker['View'][] = $listing['TrackingID'];
        
        // Listing Amenities
        $amenities = $app->db_bdx->prepare("SELECT `Type` FROM `bdx_listing_amenities` WHERE `SubdivisionID` = :SubdivisionID AND `ListingID` = :ListingID ORDER BY `Type`;");
        $amenities->execute(array('SubdivisionID' => $listing['SubdivisionID'], 'ListingID' => $listing['ListingID']));
        
        // Listing Photos
        $listing['Images'] = Listing::getImages($listing, $app->db_bdx);
        
        // Listing Features
        if ($amenities->rowCount() > 0) {
            while ($amenity = $amenities->fetchColumn()) {
                $amenity = trim(preg_replace('/[A-Z]/', ' $0', $amenity));
                $listing['Amenities'][] = $amenity;
            }
        }
        
        // Get Listing Details
        $listingDetails = Listing::getDetails();
        
        // Process Listing Details
        $listingDetails = Listing::processDetails($listingDetails, $listing);
        
        // Load Community from DB
        $result = $app->db_bdx->prepare("SELECT * FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` WHERE `SubdivisionID` = :SubdivisionID;");
        $result->execute(array('SubdivisionID' => $community));
        $community = $result->fetch();
        
        if (!empty($community)) {
            // Parse Community
            $community = Community::parse($community, $app);
        
            // Get Community details
            $communityDetails = Community::getDetails();
        
            // Process Community details
            $communityDetails = Community::processDetails($communityDetails, $community);
        }
        
        // Count the # of other homes in this subdivision
        $count_query = $app->db_bdx->prepare("SELECT COUNT(`id`) as `total` FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` WHERE `SubdivisionID` = :SubdivisionID AND `ListingID` != :ListingID");
        
        // Execute query
        $count_query->execute(array(
                'ListingID'     => $listing['ListingID'],
                'SubdivisionID' => $community['SubdivisionID']
        ));
        
        // Get Count
        $count = $count_query->fetch();
        
        if (!empty($count['total'])) {
            // Set Page Limit (Default 12)
            $page_limit = !empty($app->bdx_settings['similar_listing_page_limit']) ? $app->bdx_settings['similar_listing_page_limit'] : 12;
        
            // Get Current Page
            $current_page = (!empty($_REQUEST['bdx-p']) ? $_REQUEST['bdx-p'] : 1);
        
            // Pagination
            $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('listing', array('state' => Util::slugify($app->states[$listing['State']]), 'city' => Util::slugify($listing['City']), 'community' => $listing['SubdivisionID'], 'listing' => $listing['ListingID'])));
        
            // Max Page Exceeded, Show Last Page, Send 404
            if ($pagination['pages'] > 0 && $_REQUEST['bdx-p'] > $pagination['pages']) {
                $current_page = $pagination['pages'];
                $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('listing', array('state' => Util::slugify($app->states[$listing['State']]), 'city' => Util::slugify($listing['City']), 'community' => $listing['SubdivisionID'], 'listing' => $listing['ListingID'])));
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            // Invalid Page Requested, Show First Page, Send 404
            } else if (isset($_REQUEST['bdx-p']) && $_REQUEST['bdx-p'] < 1) {
                $current_page = 1;
                $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('listing', array('state' => Util::slugify($app->states[$listing['State']]), 'city' => Util::slugify($listing['City']), 'community' => $listing['SubdivisionID'], 'listing' => $listing['ListingID'])));
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            }
        
            // Calculate SQL Limit
            $sql_limit = Util::buildSqlLimit($count['total'], $page_limit, $current_page);
        
            // Search for more homes in this Subdivision
            $searchQuery = $app->db_bdx->prepare("SELECT 'Listing' AS `Type`, `Listing`.*, `Subdivision`.*"
                    . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                    . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                    . " WHERE `Listing`.`SubdivisionID` = :SubdivisionID AND `Listing`.`ListingID` != :ListingID"
                    . " GROUP BY `Listing`.`ListingID`"
                    . " ORDER BY `Listing`.`BasePrice` DESC"
                    . $sql_limit
                    . ";");
        
            // Find Listings
            $searchQuery->execute(array(
                    'ListingID'     => $listing['ListingID'],
                    'SubdivisionID' => $community['SubdivisionID']
            ));
                
            while ($result = $searchQuery->fetch()) {
                // Parse Listing
                $result = Listing::parse($result, $app);
                    
                // Add to BDX tracker
                $BDXTracker['Search'][] = $result['TrackingID'];
                    
                // Get Listing Image
                $result['Image'] = Listing::getImages($result, $app->db_bdx, true);
                    
                // Add to results array
                $results[] = $result;
            }
        }
            
        // Build breadcrumb options
        if (!empty($listing) && !empty($community)) {
            if ((Settings::getInstance()->STATES === true && (empty($app->bdx_settings['states']) || (is_array($app->bdx_settings['states']) && count($app->bdx_settings['states']) > 1))) ||
                    (is_array(Settings::getInstance()->STATES) && count(Settings::getInstance()->STATES) > 1)) {
                $breadcrumbOptions[] = array('Link' => $app->urlFor('state', array('state' => Util::slugify($app->states[$community['State']]))), 'Title' => $app->states[$community['State']]);
            }
            $breadcrumbOptions[] = array('Link' => $app->urlFor('search', array('search' => 'communities', 'state' => Util::slugify($app->states[$community['State']]))) . '?search[City]=' . urlencode($community['City']), 'Title' => $community['City']);
            $breadcrumbOptions[] = array(
                    'Link' =>  $app->urlFor('community', array(
                            'state' => Util::slugify($app->states[$community['State']]),
                            'city' => Util::slugify($community['City']),
                            'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),
                    )),
                    'Title' => $community['SubdivisionName']);
            $breadcrumbOptions[] = array('Title' => $listing['PlanName']);
        }
        
        // Get Agent info for the inquiry form
        if (Settings::getInstance()->FRAMEWORK) {
            if (!empty($user) && $user->isValid()) {
                $query = $app->db->query("SELECT `id`, `first_name`, `last_name`, `cell_phone`, `title`, `image`, `email` FROM `agents` WHERE `id` = " . $app->db->quote($user->info('agent')) . " LIMIT 1");
            } else {
                // Escape agents
                if (!empty($app->bdx_settings['cta_agents']) && is_array($app->bdx_settings['cta_agents'])) {
                    $quoted_agents = array();
                    foreach ($app->bdx_settings['cta_agents'] as $ag) {
                        $quoted_agents[] = $app->db_bdx->quote($ag);
                    }
                }
                    
                // Perform Query
                $query = $app->db->query("SELECT `id`, `first_name`, `last_name`, `cell_phone`, `title`, `image`, `email` FROM `agents` "
                        . (!empty($quoted_agents) ? " WHERE `id` IN (" . implode(',', $quoted_agents) . ")" : '')
                        . " ORDER BY RAND() LIMIT 1");
            }
            $agent = $query->fetch();
        } else {
            // @TODO Setup something for the standalone
        }
        
        // Get Lender info for the inquiry form
        if (Settings::getInstance()->FRAMEWORK && !empty(\Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) {
            // Get assigned lender (if exists)
            $lender = $user->info('lender');
            if (!empty($user) && $user->isValid() && !empty($lender)) {
                $query = $app->db->query("SELECT `id`, `first_name`, `last_name`, `cell_phone`, `email` "
                        . "FROM `lenders` "
                        . "WHERE `id` = " . $app->db->quote($lender) . " LIMIT 1");
            } else {
                // Escape lenders
                if (!empty($app->bdx_settings['cta_lenders']) && is_array($app->bdx_settings['cta_lenders'])) {
                    $quoted_lenders = array();
                    foreach ($app->bdx_settings['cta_lenders'] as $lndr) {
                        $quoted_lenders[] = $app->db_bdx->quote($lndr);
                    }
                }
                // Get lender from rotation
                $query = $app->db->query("SELECT `id`, `first_name`, `last_name`, `cell_phone`, `email` "
                        . "FROM `lenders` "
                        . (!empty($quoted_lenders) ? " WHERE `id` IN (" . implode(',', $quoted_lenders) . ")" : "") . " ORDER BY RAND() LIMIT 1");
            }
            $lender = $query->fetch();
            // Get lender image (if exists)
            if (!empty($lender)) {
                $query = $app->db->query("SELECT `file` "
                        . "FROM `cms_uploads` "
                        . "WHERE `row` = " . $app->db->quote($lender['id']) . " AND `type` = 'lender' LIMIT 1");
                $image = $query->fetch();
                if (!empty($image)) {
                    $lender['file'] = $image['file'];
                }
            }
        } else {
            // @TODO Setup something for the standalone
        }
        
        // Build Meta Info
        $app->page_title = str_replace('{PlanName}', $listing['PlanName'], Settings::getInstance()->META['BDX_DETAILS_PAGE_TITLE']);
        $app->page_title = str_replace('{ListingType}', $listing['ListingType'], $app->page_title);
        $app->page_title = str_replace('{ID}', $listing['id'], $app->page_title);
        
        // Meta Description
        $app->meta_description = str_replace('{Description}', $listing['Description'], Settings::getInstance()->META['BDX_DETAILS_META_DESCRIPTION']);
        $app->meta_description = str_replace('{ID}', $listing['id'], $app->meta_description);
        
        // Caniconial if pagination
        if (!empty($_REQUEST['bdx-p'])) {
            $app->canonical = $app->urlFor('listing', array(
                    'state' => Util::slugify($app->states[$listing['State']]),
                    'city' => Util::slugify($listing['City']),
                    'community' => $listing['SubdivisionID'],
                    'listing' => $listing['ListingID']));
        }
        
        // Render Page
        require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/pages/listing.tpl');
    } else {
        // 404
        require(Settings::getInstance()->DIRS['BUILDER'] . 'php/pages/404.php');
    }
    
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
