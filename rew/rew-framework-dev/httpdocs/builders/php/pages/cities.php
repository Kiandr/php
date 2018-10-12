<?php

namespace BDX;

try {
    // Build state list from settings
    $states = State::getStateSettings($app->bdx_settings);
    
    // Redirect if current state is not in state list
    if (!empty($states) && !in_array($app->state, $states)) {
        $app->redirect(Settings::getInstance()->SETTINGS['URL_BUILDERS']);
    }
    
    // Initialize Results/Cities Arrays
    $results = array();
    
    // Initialize Breadcrumb Array
    $breadcrumbOptions = array();
            
    if (!empty($app->state)) {
        // Add state to SQL where
        $sql_where = " WHERE `Subdivision`.`State` = :state";

        // Build Where if city settings are populated
        if (!empty($app->bdx_settings['states'][$app->state]['cities']) && is_array($app->bdx_settings['states'][$app->state]['cities'])) {
            $quoted_cities = array();
            foreach ($app->bdx_settings['states'][$app->state]['cities'] as $city) {
                $quoted_cities[] = $app->db_bdx->quote($city);
            }
            $sql_where .= " AND `Subdivision`.`City` IN  (" . implode(',', $quoted_cities) . ")";
        }
        
        // Count the # of Cities within the State
        $count_query = $app->db_bdx->prepare("SELECT COUNT(DISTINCT `City`) as `total` FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision`" . $sql_where);

        // Execute query
        $count_query->execute(array('state' => $app->state));
        
        // Get Count
        $count = $count_query->fetch();
        
        if (!empty($count['total'])) {
            // Set Page Limit (Default 12)
            $page_limit = !empty($app->bdx_settings['city_page_limit']) ? $app->bdx_settings['city_page_limit'] : 12;
            
            // Get Current Page
            $current_page = (!empty($_REQUEST['bdx-p']) ? $_REQUEST['bdx-p'] : 1);
            
            // Pagination
            $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('state', array('state' => Util::slugify($app->stateName))));
                    
            // Max Page Exceeded, Show Last Page, Send 404
            if ($pagination['pages'] > 0 && $_REQUEST['bdx-p'] > $pagination['pages']) {
                $current_page = $pagination['pages'];
                $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('state', array('state' => Util::slugify($app->stateName))));
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            // Invalid Page Requested, Show First Page, Send 404
            } else if (isset($_REQUEST['bdx-p']) && $_REQUEST['bdx-p'] < 1) {
                $current_page = 1;
                $pagination = Util::generatePaginationBar($count['total'], $current_page, $page_limit, $app->urlFor('state', array('state' => Util::slugify($app->stateName))));
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
            }
            
            // Calculate SQL Limit
            $sql_limit = Util::buildSqlLimit($count['total'], $page_limit, $current_page);
                        
            // Find # of Communities & Listings in each City, State
            $query = $app->db_bdx->prepare("SELECT `Subdivision`.`City`, `Subdivision`.`State`, COUNT(DISTINCT `Subdivision`.`SubdivisionID`) AS `Communities`, COUNT(DISTINCT `Listing`.`ListingID`) AS `Listings`"
                . ", GROUP_CONCAT(DISTINCT `Subdivision`.`SubdivisionID` ORDER BY `Subdivision`.`PriceFrom` DESC SEPARATOR ',') AS `SubdivisionList`"
                . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
                . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
                . $sql_where
                . " GROUP BY `Subdivision`.`State`, `Subdivision`.`City`"
                . " ORDER BY `Listings` DESC"
                . $sql_limit
            . ";");
            
            // Execute query
            $query->execute(array('state' => $app->state));
            
            // Get Total
            $total = $query->rowCount();
        
            // Populate Results Array
            while ($result = $query->fetch()) {
                // Parse State
                $result = City::parse($result, $app, $app->stateName);
                    
                $result['Images'] = City::getImages($result, $app, $app->db_bdx);
                
                // Add to results array
                $results[] = $result;
            }

            // Directly forward to the search page if there is only one city that has results
            if (count($results) == 1 && empty($_GET['bdx-p'])) {
                $app->redirect($results[0]['Link']);
            }
            
            // Get price options for subdivision search
            $priceOptions = Util::getPriceOptions();
            
            // Build search URL for subdivision and homeplan search
            $communityUrl = $app->urlFor('search', array('search' => 'communities', 'state' => Util::slugify($app->stateName)));
            $homeUrl = $app->urlFor('search', array('search' => 'homes', 'state' => Util::slugify($app->stateName)));
                        
            // Get Search Criteria array for homeplan search
            $criteria = Util::getSearchCriteria($app->db_bdx, $app->state, $app->bdx_settings['states'][$app->state]);
                        
            // Build breadcrumb options
            if ((Settings::getInstance()->STATES === true && (empty($app->bdx_settings['states']) || (is_array($app->bdx_settings['states']) && count($app->bdx_settings['states']) > 1))) ||
                (is_array(Settings::getInstance()->STATES) && count(Settings::getInstance()->STATES) > 1)) {
                $breadcrumbOptions[] = array('Title' => $app->stateName);
            }
        }
    }
    
    // Build Meta Info
    $app->page_title = str_replace('{State}', $app->stateName, Settings::getInstance()->META['BDX_STATE_PAGE_TITLE']);

// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}

// Render Page
require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/pages/cities.tpl');
