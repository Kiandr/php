<?php

namespace BDX;

try {
    // Initialize Results/States Array
    $results = array();
    $states = array();
                        
    // Build state list from settings
    $states = State::getStateSettings($app->bdx_settings);
            
    // Build Where if states settings are populated
    $sql_where = '';
    if (!empty($states) && is_array($states)) {
        $quoted_states = array();
        foreach ($states as $st) {
            $quoted_states[] = $app->db_bdx->quote($st);
        }
        $sql_where = " WHERE `Subdivision`.`State` IN (" . implode(',', $quoted_states) . ")";
    }
        
    // Count the # of states
    $count_query = $app->db_bdx->query("SELECT COUNT(DISTINCT `State`) as `total` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision`" . $sql_where);
        
    // Get Count
    $count = $count_query->fetch();
    
    if (!empty($count['total'])) {
        // Set Page Limit (Default 12)
        $page_limit = !empty($app->bdx_settings['state_page_limit']) ? $app->bdx_settings['state_page_limit'] : 12;
        
        // Get Current Page
        $current_page = !empty($_REQUEST['bdx-p']) ? $_REQUEST['bdx-p'] : 1;
            
        // Pagination
        $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, Settings::getInstance()->SETTINGS['URL_BUILDERS'] . '/');
        
        // Max Page Exceeded, Show Last Page, Send 404
        if ($pagination['pages'] > 0 && $_REQUEST['bdx-p'] > $pagination['pages']) {
            $current_page = $pagination['pages'];
            $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, Settings::getInstance()->SETTINGS['URL_BUILDERS'] . '/');
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
        // Invalid Page Requested, Show First Page, Send 404
        } else if (isset($_REQUEST['bdx-p']) && $_REQUEST['bdx-p'] < 1) {
            $current_page = 1;
            $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, Settings::getInstance()->SETTINGS['URL_BUILDERS'] . '/');
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
        }
        
        // Calculate SQL Limit
        $sql_limit = Util::buildSqlLimit($count['total'], $page_limit, $current_page);
                                            
        // Find # of Communities & Listings in each City, State
        $query = $app->db_bdx->query("SELECT `Subdivision`.`State`, COUNT(DISTINCT `Subdivision`.`SubdivisionID`) AS `Communities`, COUNT(DISTINCT `Listing`.`ListingID`) AS `Listings`"
                . ", GROUP_CONCAT(DISTINCT `Subdivision`.`SubdivisionID` ORDER BY `Subdivision`.`PriceFrom` DESC SEPARATOR ',') AS `SubdivisionList`"
                . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                . $sql_where
                . " GROUP BY `Subdivision`.`State`"
                . " ORDER BY `Listings` DESC"
                . $sql_limit
                . ";");
                        
        // Get Total
        $total = $query->rowCount();
            
        // Populate Results Array
        while ($result = $query->fetch()) {
            // Parse State
            $result = State::parse($result, $app);
                    
            // Get Images
            $result['Images'] = State::getImages($result, $app, $app->db_bdx);
            
            // Add to results array
            $results[] = $result;
        }
                
        // Directly forward to the city page if there is only one state that has results
        if (count($results) == 1 && empty($_GET['bdx-p'])) {
            $app->redirect($results[0]['Link']);
        }
    }

    // Build Meta Info
    $app->page_title = Settings::getInstance()->META['BDX_MAIN_PAGE_TITLE'];
    
    // Render Page
    require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/pages/states.tpl');
    
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
