<?php

namespace BDX;

try {
    // Initialize Results & Tracker Array
    $results = array();
    $BDXTracker = array();
    
    // Initialize Breadcrumb Array
    $breadcrumbOptions = array();
    
    // Load community from database
    $result = $app->db_bdx->prepare("SELECT * FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` WHERE `SubdivisionID` = :SubdivisionID;");
    $result->execute(array('SubdivisionID' => $community));
    $community = $result->fetch();
                    
    if (!empty($community)) {
        // Community Photos
        $community['Images'] = Community::getImages($community, $app->db_bdx);
        
        // Parse Community
        $community = Community::parse($community, $app);
        
        // Add to BDX tracker
        $BDXTracker['View'][] = $community['TrackingID'];
        
        // Community Features
        $features = array_filter(array(
            'Build On Your Lot' => $community['BuildOnYourLot'],
            'Condo / Town home' => $community['MultiFamily'],
            'Golf Course'       => $community['GolfCourse'],
            'Pool'              => $community['Pool'],
            'Greenbelt'         => $community['Greenbelt'],
            'Views'             => $community['Views'],
            'Park'              => $community['Park'],
            'Sport Facilities'  => $community['SportFacilities']
        ));
    
        // Count the # of Avaliable Listings
        $count_query = $app->db_bdx->prepare("SELECT COUNT(`id`) as `total` FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` WHERE `SubdivisionID` = :SubdivisionID");
        
        // Execute Query
        $count_query->execute(array('SubdivisionID' => $community['SubdivisionID']));
                
        // Fetch!
        $count = $count_query->fetch();
        
        if (!empty($count['total'])) {
            // Set Page Limit (Default 12)
            $page_limit = !empty($app->bdx_settings['listing_page_limit']) ? $app->bdx_settings['listing_page_limit'] : 12;
            
            // Get Current Page
            $current_page = (!empty($_REQUEST['bdx-p']) ? $_REQUEST['bdx-p'] : 1);
            
            // Pagination
            $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('community', array(
                    'state' => Util::slugify($app->states[$community['State']]),
                    'city' => Util::slugify($community['City']),
                    'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),
            )));

            // Max Page Exceeded, Show Last Page, Send 404
            if ($pagination['pages'] > 0 && $_REQUEST['bdx-p'] > $pagination['pages']) {
                $current_page = $pagination['pages'];
                $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('community', array(
                        'state' => Util::slugify($app->states[$community['State']]),
                        'city' => Util::slugify($community['City']),
                        'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),
                )));
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            // Invalid Page Requested, Show First Page, Send 404
            } else if (isset($_REQUEST['bdx-p']) && $_REQUEST['bdx-p'] < 1) {
                $current_page = 1;
                $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('community', array(
                        'state' => Util::slugify($app->states[$community['State']]),
                        'city' => Util::slugify($community['City']),
                        'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),
                )));
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            }
            
            // Calculate SQL Limit
            $sql_limit = Util::buildSqlLimit($count['total'], $page_limit, $current_page);
            
            // Search for Available Listings
            $listings = $app->db_bdx->prepare("SELECT 'Listing' AS `Type`, `Listing`.*, `Subdivision`.*"
                    . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                    . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                    . " WHERE `Listing`.`SubdivisionID` = :SubdivisionID"
                    . " GROUP BY `Listing`.`ListingID`"
                    . " ORDER BY `Listing`.`BasePrice` DESC"
                    . $sql_limit
                    . ";");
            
            // Find Listings
            $listings->execute(array('SubdivisionID' => $community['SubdivisionID']));
            
            // Total # of Results
            $total = $listings->rowCount();
            
            while ($result = $listings->fetch()) {
                // Parse Listing
                $result = Listing::parse($result, $app);
                
                // Get Listing Images
                $result['Image'] = Listing::getImages($result, $app->db_bdx, true);
                
                // Add to BDX tracker
                $BDXTracker['Search'][] = $result['TrackingID'];
                
                // Add to results array
                $results[] = $result;
            }
        }
        
        // Build breadcrumb options
        if ((Settings::getInstance()->STATES === true && (empty($app->bdx_settings['states']) || (is_array($app->bdx_settings['states']) && count($app->bdx_settings['states']) > 1))) ||
            (is_array(Settings::getInstance()->STATES) && count(Settings::getInstance()->STATES) > 1)) {
            $breadcrumbOptions[] = array('Link' => $app->urlFor('state', array('state' => Util::slugify($app->states[$community['State']]))), 'Title' => $app->states[$community['State']]);
        }
        $breadcrumbOptions[] = array('Link' => $app->urlFor('search', array('search' => 'communities', 'state' =>  Util::slugify($app->states[$community['State']]))) . '?search[City]=' . urlencode($community['City']), 'Title' => $community['City']);
        $breadcrumbOptions[] = array('Title' => $community['SubdivisionName']);
        
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
        $app->page_title = str_replace('{Community}', $community['SubdivisionName'], Settings::getInstance()->META['BDX_COMMUNITY_PAGE_TITLE']);
        $app->page_title = str_replace('{City}', $community['City'], $app->page_title);
        $app->page_title = str_replace('{State}', $community['State'], $app->page_title);
        $app->page_title = str_replace('{ID}', $community['SubdivisionID'], $app->page_title);
        
        // Meta Description
        $app->meta_description = str_replace('{Description}', $community['SubDescription'], Settings::getInstance()->META['BDX_COMMUNITY_META_DESCRIPTION']);
        $app->meta_description = str_replace('{ID}', $community['SubdivisionID'], $app->meta_description);
        
        // Caniconial if pagination
        if (!empty($_REQUEST['bdx-p'])) {
            $app->canonical = $app->urlFor('community', array(
                    'state' => Util::slugify($app->states[$community['State']]),
                    'city' => Util::slugify($community['City']),
                    'community' => $community['SubdivisionID'] . '-' . Util::slugify($community['SubdivisionName']),
            ));
        }
        
        // Render Page
        require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/pages/community.tpl');
    } else {
        // 404
        require(Settings::getInstance()->DIRS['BUILDER'] . 'php/pages/404.php');
    }
        
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
