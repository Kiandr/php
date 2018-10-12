<?php

namespace BDX;

try {
    // Initialize Results & Tracker Array
    $results = array();
    $BDXTracker = array();
    
    // Initialize Breadcrumb Array
    $breadcrumbOptions = array();
    
    // Search Criteria
    $criteria = Util::getSearchCriteria($app->db_bdx, $app->state, $app->bdx_settings);
    
    // Current Search
    $searchData = $app->request->get('search');
    if (empty($searchData)) {
        $searchData = array();
    }
        
    // Form Fields that accept Multiples
    $multiples = Util::getMultiples();
    
    foreach ($multiples as $multiple) {
        if (!empty($searchData[$multiple]) && is_string($searchData[$multiple])) {
            $searchData[$multiple] = explode(',', $searchData[$multiple]);
            $searchData[$multiple] = array_map('trim', $searchData[$multiple]);
        }
    }
    
    // Modify Search Data
    if (!empty($searchData['NotPreSale']) && $searchData['NotPreSale'] == 'Y') {
        if (!empty($criteria['Status']['options']) && is_array($criteria['Status']['options'])) {
            foreach ($criteria['Status']['options'] as $option) {
                if ($option['value'] != 'ComingSoon') {
                    $searchData['Status'][] = $option['value'];
                }
            }
        }
    }
    
    // Remove cities that are not part of the backend settings
    if (!empty($app->bdx_settings['states'][$app->state]['cities']) && is_array($app->bdx_settings['states'][$app->state]['cities']) && !empty($searchData['City'])) {
        $searchData['City'] = (array) $searchData['City'];
        foreach ($searchData['City'] as $key => $val) {
            if (!in_array($val, $app->bdx_settings['states'][$app->state]['cities'])) {
                unset($searchData['City'][$key]);
            }
        }
    }
    
    // Default data to backend cities if none are set
    if (empty($searchData['City']) && !empty($app->bdx_settings['states'][$app->state]['cities']) && is_array($app->bdx_settings['states'][$app->state]['cities'])) {
        foreach ($app->bdx_settings['states'][$app->state]['cities'] as $city) {
            $searchData['City'][] = $city;
        }
    }
    
    // Current Search Criteria
    $andQueryString = array();
    $orQueryString = array();
    $andQueryParams = array();
    $orQueryParams = array();
    
    if (!empty($searchData)) {
        foreach ($searchData as $field => $value) {
            if (is_array($value)) {
                $value = array_map('trim', $value);
            } else {
                $value = trim($value);
            }
            if (empty($value)) {
                continue;
            }
            $field = $criteria[$field];
            if (!empty($field) && is_array($field)) {
                if (!empty($field['criteria']) && is_array($field['criteria'])) {
                    if (count($field['criteria']) > 1) {
                        foreach ($field['criteria'] as $currentCriteria) {
                            if (!empty($currentCriteria) && is_array($currentCriteria)) {
                                foreach ($currentCriteria as $match => $column) {
                                    if (in_array($match, array('=', '!=', 'LIKE', '>', '>=', '<', '<='))) {
                                        if (is_array($value)) {
                                            $multipleValueQueryString = array();
                                            foreach ($value as $currentValue) {
                                                if (!empty($currentValue)) {
                                                    $multipleValueQueryString[] = $column . ' ' . $match . ' ?';
                                                    $orQueryParams[] = $match === 'LIKE' ? '%' . $currentValue . '%' : $currentValue;
                                                }
                                            }
                                            $orQueryString[] = "(" . implode(" OR ", $multipleValueQueryString) . ")";
                                        } else {
                                            $orQueryString[] = $column . ' ' . $match . ' ?';
                                            $orQueryParams[] = $match === 'LIKE' ? '%' . $value . '%' : $value;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($field['criteria'] as $match => $column) {
                            if (in_array($match, array('=', '!=', 'LIKE', '>', '>=', '<', '<='))) {
                                if (is_array($value)) {
                                    $multipleValueQueryString = array();
                                    foreach ($value as $currentValue) {
                                        if (!empty($currentValue)) {
                                            $multipleValueQueryString[] = $column . ' ' . $match . ' ?';
                                            $andQueryParams[] = $match === 'LIKE' ? '%' . $currentValue . '%' : $currentValue;
                                        }
                                    }
                                    $andQueryString[] = "(" . implode(" OR ", $multipleValueQueryString) . ")";
                                } else {
                                    $andQueryString[] = $column . ' ' . $match . ' ?';
                                    $andQueryParams[] = $match === 'LIKE' ? '%' . $value . '%' : $value;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    // Build Query String
    $queryString = (!empty($andQueryString) ? implode(" AND ", $andQueryString) : "");
    if (!empty($queryString)) {
        if (!empty($orQueryString)) {
            $queryString .= " AND (" . implode(" OR ", $orQueryString) . ") AND `Subdivision`.`State` = '" . $app->state . "'";
        } else {
            $queryString .= " AND `Subdivision`.`State` = '" . $app->state . "'";
        }
    } else {
        if (!empty($orQueryString)) {
            $queryString .= "(" . implode(" OR ", $orQueryString) . ") AND `Subdivision`.`State` = '" . $app->state . "'";
        } else {
            $queryString .= " `Subdivision`.`State` = '" . $app->state . "'";
        }
    }
    
    // Build Query Param array
    $queryParams = array_merge($andQueryParams, $orQueryParams);
    
    // Get # of Available Communities & Listings
    $countQuery = $app->db_bdx->prepare("SELECT COUNT(DISTINCT `Subdivision`.`SubdivisionID`) AS `Communities`, COUNT(DISTINCT `Listing`.`ListingID`) AS `Listings`"
    . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
    . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
    . (!empty($queryString) ? " WHERE " . $queryString : '')
    . ";");
    
    // Search Count
    $countQuery->execute($queryParams);
    
    // # of Results
    $total = $countQuery->fetch();
    
    // Set Page Limit (Default 12)
    if (!empty($searchData['page_limit'])) {
        $page_limit = $searchData['page_limit'];
    } else if ($app->bdx_settings['community_page_limit']) {
        $page_limit = $app->bdx_settings['community_page_limit'];
    } else {
        $page_limit = 12;
    }
    
    // Get Current Page
    if (!empty($app->snippet)) {
        $current_page = (!empty($_REQUEST['bdx-p' . $_REQUEST['bdx-snippet']]) ? $_REQUEST['bdx-p' . $_REQUEST['bdx-snippet']] : 1);
    } else {
        $current_page = (!empty($_REQUEST['bdx-p']) ? $_REQUEST['bdx-p'] : 1);
    }
    
    
    // Total # of Listings or Communities for pagination
    $count = ($search == 'homes') ? $total['Listings'] : $total['Communities'];
    
    // Build Pagination URL
    if (empty($app->snippet)) {
        if ($search == 'homes') {
            $page_url = $app->urlFor('search', array('search' => 'homes', 'state' => Util::slugify($app->stateName)));
        } else {
            $page_url = $app->urlFor('search', array('search' => 'communities', 'state' => Util::slugify($app->stateName)));
        }
    } else {
        $page_url = strtok($_SERVER["REQUEST_URI"], '?');
    }
    
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
    
    // Order / Sort
    if (!empty($searchData['sort_by']) && in_array($searchData['sort_by'], Util::getSortOptions())) {
        list ($searchData['sort'], $searchData['order'], $searchData['sort_type']) = explode('-', $searchData['sort_by'], 3);
        // Build order by
        $sql_order = " ORDER BY `" . $searchData['sort_type'] .  "`." . $searchData['order'] . " " . $searchData['sort'];
    } else {
        if ($search == 'homes') {
            $sql_order = " ORDER BY `Listing`.`BasePrice` DESC";
        } else {
            $sql_order = " ORDER BY `Subdivision`.`SubdivisionName` ASC";
        }
    }
    
    // Search Homes
    if ($search == 'homes') {
        // Search for Available Listings
        $searchQuery = $app->db_bdx->prepare("SELECT 'Listing' AS `Type`, `Listing`.*, `Subdivision`.*"
        . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
        . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
        . (!empty($queryString) ? " WHERE " . $queryString : '')
        . " GROUP BY `Listing`.`ListingID`"
        . $sql_order
        . $sql_limit
        . ";");
    } else {
        // Search for Available Communities
        $searchQuery = $app->db_bdx->prepare("SELECT 'Subdivision' AS `Type`, `Subdivision`.*, COUNT(DISTINCT `Listing`.`ListingID`) AS `Listings`"
        . " FROM `" . Settings::getInstance()->TABLES['BDX_LISTINGS'] . "` `Listing`"
        . " LEFT JOIN `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` `Subdivision` ON `Listing`.`SubdivisionID` = `Subdivision`.`SubdivisionID`"
        . (!empty($queryString) ? " WHERE " . $queryString : '')
        . " GROUP BY `Subdivision`.`SubdivisionID`"
        . $sql_order
        . $sql_limit
        . ";");
    }
    
    // Execute Search
    $searchQuery->execute($queryParams);
    
    while ($result = $searchQuery->fetch()) {
        if ($result['Type'] == 'Listing') {
            // Parse Listing
            $result = Listing::parse($result, $app);
            
            // Add to BDX tracker
            $BDXTracker['Search'][] = $result['TrackingID'];
                        
            // Get Listing Images
            $result['Image'] = Listing::getImages($result, $app->db_bdx, true);
        } else if ($result['Type'] == 'Subdivision') {
            // Parse Community
            $result = Community::parse($result, $app);
                        
            // Add to BDX tracker
            $BDXTracker['Search'][] = $result['TrackingID'];
            
            // Get Community Images
            $result['Image'] = Community::getImages($result, $app->db_bdx, true);
        }
                
        // Add to results array
        $results[] = $result;
    }
        
    // Build search URLs
    $communityUrl = $app->urlFor('search', array('search' => 'communities', 'state' => Util::slugify($app->stateName)));
    $homeUrl = $app->urlFor('search', array('search' => 'homes', 'state' => Util::slugify($app->stateName)));
    
    // Ensure pagination is not included in GET params when building search URLs
    $getParams = $_GET;
    unset($getParams['bdx-p']);
    
    // Build search URLs with search params
    $communitySearchUrl = $communityUrl . (!empty($_GET) ? '?' . http_build_query($getParams) : '');
    $homeSearchUrl = $homeUrl . (!empty($_GET) ? '?' . http_build_query($getParams) : '');
    
    // Build breadcrumb options
    if ((Settings::getInstance()->STATES === true && (empty($app->bdx_settings['states']) || (is_array($app->bdx_settings['states']) && count($app->bdx_settings['states']) > 1))) ||
        (is_array(Settings::getInstance()->STATES) && count(Settings::getInstance()->STATES) > 1)) {
        $breadcrumbOptions[] = array('Link' => $app->urlFor('state', array('state' => Util::slugify($app->stateName))), 'Title' => $app->stateName);
    }
    if (!is_array($_GET['search']['City'])) {
        $breadcrumbOptions[] = array('Title' => $_GET['search']['City']);
    }
    
    // Build search title
    if (!empty($total['Communities']) && !empty($total['Listings'])) {
        $searchTitle = '<h1>New Home Communities in ' . (!empty($_GET['search']['City']) && !is_array($_GET['search']['City']) ? $_GET['search']['City'] . ', ' : '') . $app->stateName . '</h1>';
        if ($total['Communities'] == 1) {
            $searchTitle .= '<h2>Found 1 Community';
        } else {
            $searchTitle .= '<h2>Found ' . (!empty($total['Communities']) ? number_format($total['Communities']) : 0) . ' Communities';
        }
        if ($total['Listings'] == 1) {
            $searchTitle .= ' with 1 new home</h2>';
        } else {
            $searchTitle .= ' with ' . (!empty($total['Listings']) ? number_format($total['Listings']) : 0) . ' New Homes</h2>';
        }
    } else {
        $searchTitle = 'No matching communities or homes found.';
    }
    
    // Render Sidebar
    if (empty($app->snippet)) {
        // Remove Location panel from panel set
        unset($criteria['Location']);
        
        // Render sidebar
        require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/forms/sidebar-search.tpl');
    }
    
    // Render Page
    require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/pages/search.tpl');
    
    // Build Meta Info
    if (!empty($_GET['search']['City'])) {
        $meta_city = (is_array($_GET['search']['City']) ? $_GET['search']['City'][0] : $_GET['search']['City']) . ', ';
    } else {
        $meta_city = "";
    }
    $app->page_title = str_replace('{City}', $meta_city, Settings::getInstance()->META['BDX_SEARCH_PAGE_TITLE']);
    $app->page_title = str_replace('{State}', $app->stateName, $app->page_title);
        
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
