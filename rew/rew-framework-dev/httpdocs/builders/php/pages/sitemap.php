<?php

namespace BDX;

try {
    // Initialize Results & Query Parameter Array
    $groups = array();
    
    // Build state list from settings
    if (!empty($app->bdx_settings['states']) && is_array($app->bdx_settings['states'])) {
        if (is_array(Settings::getInstance()->STATES)) {
            foreach ($app->bdx_settings['states'] as $key => $val) {
                if (in_array($key, Settings::getInstance()->STATES)) {
                    $states[] = $key;
                }
            }
        } else {
            foreach ($app->bdx_settings['states'] as $key => $val) {
                $states[] = $key;
            }
        }
    } else {
        if (is_array(Settings::getInstance()->STATES)) {
            foreach (Settings::getInstance()->STATES as $state) {
                $states[] = $state;
            }
        }
    }
    
    // Build Where if states settings are populated
    $sql_where = '';
    if (!empty($states) && is_array($states)) {
        $quoted_states = array();
        foreach ($states as $st) {
            $quoted_states[] = $app->db_bdx->quote($st);
        }
        $sql_where = " WHERE `Subdivision`.`State` IN (" . implode(',', $quoted_states) . ")";
    }
        
    // Get # of Available Communities & Listings
    $countQuery = $app->db_bdx->prepare("SELECT COUNT(DISTINCT `Subdivision`.`SubdivisionID`) AS `Communities`, COUNT(DISTINCT `Listing`.`ListingID`) AS `Listings`"
            . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
            . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
            . $sql_where
            . ";");
        
    // Search Count
    $countQuery->execute();
    
    // Page Limit
    $page_limit = 75;
    
    // # of Results
    $total = $countQuery->fetch();
    
    // Total # of Listings or Communities for pagination
    $count = ($search == 'homes') ? $total['Listings'] : $total['Communities'];
    
    // Get Current Page
    $current_page = (!empty($_REQUEST['bdx-p']) ? $_REQUEST['bdx-p'] : 1);
    
    // Page URL
    $page_url = strtok($_SERVER["REQUEST_URI"], '?');
    
    // Pagination
    $pagination = Util::generatePaginationBar($count, $current_page, $page_limit, $page_url);
    
    // Max Page Exceeded, Show Last Page, Send 404
    if ($pagination['pages'] > 0 && $_REQUEST['bdx-p'] > $pagination['pages']) {
        $current_page = $pagination['pages'];
        $pagination = Util::generatePaginationBar($count, $current_page, $page_limit, $page_url);
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
    // Invalid Page Requested, Show First Page, Send 404
    } else if (isset($_REQUEST['bdx-p']) && $_REQUEST['bdx-p'] < 1) {
        $current_page = 1;
        $pagination = Util::generatePaginationBar($count, $current_page, $page_limit, $page_url);
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
    }

    // Calculate SQL Limit
    $sql_limit = Util::buildSqlLimit($count, $page_limit, $current_page);
    
    if ($_GET['type'] == 'community') {
        // Build SQL order
        $sql_order = " ORDER BY `Subdivision`.`City` ASC";
        
        // Search for Avaliable Communities
        $searchQuery = $app->db_bdx->prepare("SELECT 'Subdivision' AS `Type`, `Listing`.*, `Subdivision`.*"
                . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                . $sql_where
                . " GROUP BY `Subdivision`.`SubdivisionID`"
                . $sql_order
                . $sql_limit
                . ";");
        
        // Set Meta Type
        $meta_type = 'Community';
    } else {
        // Build SQL order
        $sql_order = " ORDER BY `Subdivision`.`BrandName` ASC, `Subdivision`.`SubdivisionName` ASC";
        
        // Search for Available Listings
        $searchQuery = $app->db_bdx->prepare("SELECT 'Listing' AS `Type`, `Listing`.*, `Subdivision`.*"
                . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                . $sql_where
                . " GROUP BY `Listing`.`ListingID`"
                . $sql_order
                . $sql_limit
                . ";");
        
        // Set Meta Type
        $meta_type = 'Listing';
    }
        
    // Execute Search
    $searchQuery->execute();
    
    while ($result = $searchQuery->fetch()) {
        if ($result['Type'] == 'Listing') {
            // Parse Listing
            $result = Listing::parse($result, $app);
            
            // Add to results array
            $groups[$result['BrandName']][] = $result;
        } else if ($result['Type'] == 'Subdivision') {
            // Parse Community
            $result = Community::parse($result, $app);

            // Add to results array
            $groups[$result['City']][] = $result;
        }
    }
        
    // Render Page
    require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/pages/sitemap.tpl');
    
    $app->page_title = str_replace('{Type}', $meta_type, Settings::getInstance()->META['BDX_SITEMAP_PAGE_TITLE']);
    
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
