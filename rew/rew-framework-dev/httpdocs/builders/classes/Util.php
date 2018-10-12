<?php

/**
 * BDX_Util is a utility class containing methods used in our BDX Platform
 */

namespace BDX;

class Util
{
    
    /**
     * Builds search criteria and panels
     * @param object $db
     * @param string $bdx_settings
     * @return array
     */
    public static function getSearchCriteria($db, $state, $bdx_settings)
    {
                
        // Get Price options for min/max price field
        $priceOptions = self::getPriceOptions();
        
        // Escape cities
        $quoted_cities = array();
        if (!empty($bdx_settings['states'][$state]['cities']) && is_array($bdx_settings['states'][$state]['cities'])) {
            foreach ($bdx_settings['states'][$state]['cities'] as $city) {
                $quoted_cities[] = $db->quote($city);
            }
        }
        
        // Search Criteria
        $criteria = array(
            // Search by Location
            'Location' => array(
                'title'         => 'Search by Location',
                'placeholder'   => 'Enter a city, subdivision, zip, or builder name',
                'criteria'      => array(array('=' => 'Subdivision.City'), array('LIKE' => 'Subdivision.SubdivisionName'), array('=' => 'Subdivision.Zip'), array('LIKE' => 'Subdivision.BrandName')),
            ),
            // Search by City
            'City'  => array(
                'title'         => 'Search by City',
                'multiple'      => true,
                'criteria'      => array('=' => 'Subdivision.City'),
                'options'       => "SELECT City AS value, City AS title FROM `" . Settings::getInstance()->TABLES['BDX_SUBDIVISIONS'] . "` WHERE `State` = " . $db->quote($state) . (!empty($quoted_cities) ? " AND `City` IN (" . implode(',', $quoted_cities) . ")" : "") . " GROUP BY `City` ORDER BY `City` ASC;"
            ),
            // Search by Community Name
            'Community' => array(
                'title'         => 'Community Name',
                'placeholder'   => 'Community Name',
                'autocomplete'  => true,
                'criteria'      => array('LIKE' => 'Subdivision.SubdivisionName'),
            ),
            // Search by Zip Code
            'ZipCode' => array(
                'title'         => 'Search by Zip Code',
                'placeholder'   => 'Zip Code',
                'autocomplete'  => true,
                'criteria'      => array('=' => 'Subdivision.Zip'),
            ),
            // Search by Builder
            'Builder'       => array(
                'title'         => 'Search by Builder',
                'placeholder'   => 'Builder Name',
                'criteria'      => array('LIKE' => 'Subdivision.BrandName'),
                'autocomplete'  => true,
            ),
            // Plan Type (SingleFamily or MultiFamily)
            'PlanType'  => array(
                'title'         => 'Type of Home',
                'criteria'      => array('=' => 'Listing.PlanType'),
                'options'       => array(
                    array('value' => '',                'title' => 'All'),
                    array('value' => 'SingleFamily',    'title' => 'Single Family'),
                    array('value' => 'MultiFamily',     'title' => 'Multi-Family (Condo / Townhome)')
                )
            ),
            'ListingType'   => array(
                'title'         => 'Listing Type',
                'criteria'      => array('=' => 'Listing.ListingType'),
                'options'       => array(
                    array('value' => '',        'title' => 'All'),
                    array('value' => 'P',       'title' => 'Plan'),
                    array('value' => 'S',       'title' => 'Spec')
                )
            ),
            // Status
            'Status'    => array(
                'title'         => 'Status',
                'criteria'      => array('=' => 'Subdivision.Status'),
                'multiple'      => true,
                'options'       => array(
                    array('value' => 'Active',          'title' => 'Active'),
                    array('value' => 'Closeout',        'title' => 'Closeout'),
                    array('value' => 'ComingSoon',      'title' => 'Coming Soon'),
                    array('value' => 'GrandOpening',    'title' => 'Grand Opening')
                )
            ),
            // Minimum Price
            'MinPrice'          => array(
                'title'         => 'Min. Price',
                'criteria'      => array('>=' => 'Listing.BasePrice'),
                'options'       => array_merge(array(
                    array('value' => 0, 'title' => 'No Minimum')
                ), $priceOptions)
            ),
            // Maximum Price
            'MaxPrice'  => array(
                'title'         => 'Max. Price',
                'criteria'      => array('<=' => 'Listing.BasePrice'),
                'options'       => array_merge(array(
                    array('value' => 0, 'title' => 'No Maximum')
                ), $priceOptions)
            ),
            // Minimum Bedrooms
            'MinBeds'           => array(
                'title'         => 'Bedrooms',
                'criteria'      => array('>=' => 'Listing.Bedrooms'),
                'options'       => array(
                    array('value' => 0, 'title' => 'Any'),
                    array('value' => 1, 'title' => '1+'),
                    array('value' => 2, 'title' => '2+'),
                    array('value' => 3, 'title' => '3+'),
                    array('value' => 4, 'title' => '4+'),
                    array('value' => 5, 'title' => '5+')
                )
            ),
            // Minimum Bathrooms
            'MinBaths'  => array(
                'title'         => 'Bathrooms',
                'criteria'      => array('>=' => 'Listing.Baths'),
                'options'       => array(
                    array('value' => 0, 'title' => 'Any'),
                    array('value' => 1, 'title' => '1+'),
                    array('value' => 2, 'title' => '2+'),
                    array('value' => 3, 'title' => '3+'),
                    array('value' => 4, 'title' => '4+'),
                    array('value' => 5, 'title' => '5+')
                )
            ),
            // # of Stories
            'Stories'   => array(
                'title'         => 'Stories / Floors',
                'criteria'      => array('=' => 'Listing.Stories'),
                'options'       => array(
                    array('value' => 0, 'title' => 'Any'),
                    array('value' => 1, 'title' => '1'),
                    array('value' => 2, 'title' => '2'),
                    array('value' => 3, 'title' => '3')
                )
            ),
            // Minimum Garages
            'MinGarage' => array(
                'title'         => 'Garage',
                'criteria'      => array('>=' => 'Listing.Garage'),
                'options'       => array(
                    array('value' => 0, 'title' => 'Any'),
                    array('value' => 1, 'title' => '1+'),
                    array('value' => 2, 'title' => '2+'),
                    array('value' => 3, 'title' => '3+')
                )
            ),
            // Minimum Living Areas
            'MinLivingAreas'    => array(
                'title'         => 'Living Areas',
                'criteria'      => array('$gte' => 'Listing.ListingAreas'),
                'options'       => array(
                    array('value' => 0, 'title' => 'Any'),
                    array('value' => 1, 'title' => '1+'),
                    array('value' => 2, 'title' => '2+'),
                    array('value' => 3, 'title' => '3+')
                )
            ),
            // Minimum Dining Areas
            'MinDiningAreas'    => array(
                'title'         => 'Dining Areas',
                'criteria'      => array('>=' => 'Listing.DiningAreas'),
                'options'       => array(
                    array('value' => 0, 'title' => 'Any'),
                    array('value' => 1, 'title' => '1+'),
                    array('value' => 2, 'title' => '2+'),
                    array('value' => 3, 'title' => '3+')
                )
            ),
            // Location of Master Bedroom
            'MasterBedLocation' => array(
                'title'         => 'Master Bedroom',
                'criteria'      => array('=' => 'Listing.MasterBedLocation'),
                'options'       => array(
                    array('value' => '',        'title' => 'Any'),
                    array('value' => 'Down',    'title' => 'Down'),
                    array('value' => 'Up',      'title' => 'Up')
                )
            ),
            // Community Style
            'CommunityStyle'    => array(
                'title'         => 'Community Style',
                'criteria'      => array('=' => 'Subdivision.CommunityStyle'),
                'options'       => array(
                    array('value' => '',                'title' => 'Any'),
                    array('value' => 'Adult',           'title' => 'Adult Community'),
                    array('value' => 'Gated',           'title' => 'Gated Community'),
                    array('value' => 'MasterPlanned',   'title' => 'Master Planned'),
                    array('value' => 'AgeRestricted',   'title' => 'Age Restricted'),
                    array('value' => 'CondoOnly',       'title' => 'Condo Only'),
                )
            ),
            // Golf Course
            'GolfCourse'        => array(
                'title'         => 'Golf Course',
                'criteria'      => array('=' => 'Subdivision.GolfCourse'),
                'options'       => array(
                    array('value' => '',            'title' => 'No Preference'),
                    array('value' => 'Y',           'title' => 'Yes'),
                    array('value' => 'N',           'title' => 'No'),
                )
            ),
            // Pool
            'Pool'      => array(
                'title'         => 'Pool',
                'criteria'      => array('=' => 'Subdivision.Pool'),
                'options'       => array(
                    array('value' => '',            'title' => 'No Preference'),
                    array('value' => 'Y',           'title' => 'Yes'),
                    array('value' => 'N',           'title' => 'No'),
                )
            ),
        );
        
        return $criteria;
    }
    
    /*
	 * Builds price options
	 * @return array
	 */
    public static function getPriceOptions()
    {
        
        // Price Options
        $priceOptions = array();
        for ($price = 50000; $price <= 10000000; $price += 25000) {
            if (($price > 500000) && ($price <= 1000000)) {
                $price += 25000;
            }
            if (($price > 1000000) && ($price <= 3000000)) {
                $price += 75000;
            }
            if (($price > 3000000) && ($price <= 4000000)) {
                $price += 225000;
            }
            if (($price > 4000000) && ($price <= 10000000)) {
                $price += 475000;
            }
            $priceOptions[] = array('value' => $price, 'title' => '$' . number_format($price));
        }
        
        return $priceOptions;
    }

    
    /*
	 * Returns list of fields that allow multiple inputs
	 * @return array
	 */
    public static function getMultiples()
    {
        return array(
            'Location',
            'Community',
            'Zip',
            'Builder',
        );
    }
    
    /* Return pagination information
	 *
	 * @param int $total_results
	 * @param int $page
	 * @param int $page_limit
	 * @param string $url
	 * @param string $anchor
	 * @param array $query_string
	 * @return array
	 */
    public static function generatePaginationBar($total_results, $page, $page_limit, $url = '', $anchor = '', $query_string = false)
    {
        $page = (!empty($page)) ? $page : 1;
        $numofpages = ceil($total_results / $page_limit);
        $startpage = ($numofpages > 3) ? $page - 4 : "0";
        $startpage = ($startpage < 0) ? "0" : $startpage;
        $endpage = $numofpages;
        $endpage = ($numofpages > 3) ? $page + 3 : 3;
        $endpage = ($endpage > $numofpages) ? $numofpages : $endpage;
        $snippet_number = (!empty($_REQUEST['bdx-snippet']) ? $_REQUEST['bdx-snippet'] : '');
        $pagination_key = 'bdx-p' . $snippet_number;
        
        // Query String
        if (!is_array($query_string)) {
            $query_string = array();
            if (strpos($_SERVER['REQUEST_URI'], '?')) {
                parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_string);
                // unset vars we dont want carried over
                unset($query_string['search_title']);
                unset($query_string[$pagination_key]);
            }
        }
        
        // Pagination Collection
        $pagination = array();
        
        // Pagination Information
        $pagination['pages'] = $numofpages;
        $pagination['page'] = $page;
        
        // Showing Results
        $pagination['start'] = (($page * $page_limit) - $page_limit) + 1;
        $pagination['end'] = ($page * $page_limit);
                
        // Current URL
        //$url = !empty($url) ? $url : Http_Uri::getUri();
        
        if ($numofpages > 1) {
            // First/Prev Page
            if ($page != 1) {
                $pageprev = $page - 1;
                if ($pageprev == 1) {
                    $page_url = $url . (!empty($query_string) ? '?' . htmlspecialchars(http_build_query($query_string)) : '');
                } else {
                    $page_url = $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array($pagination_key => $pageprev))));
                }
                $pagination['prev'] = array(
                        'url' => $page_url . $anchor,
                        'link' => '&lt;&lt;'
                );
                if (1 <= $startpage) {
                    $pagination['links'][] = array(
                            'url' => $url . (!empty($query_string) ? '?' . http_build_query($query_string) : '') . $anchor,
                            'link' => '1'
                    );
                }
            }
            // Between Pages
            for ($i = $startpage; $i < $endpage; $i++) {
                $real_page = $i + 1;
                if ($real_page == 1) {
                    $page_url = $url . (!empty($query_string) ? '?' . htmlspecialchars(http_build_query($query_string)) : '');
                } else {
                    $page_url = $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array($pagination_key => $real_page))));
                }
                $pagination['links'][] = array(
                        'url' => $page_url . $anchor,
                        'link' => $real_page,
                        'active' => ($real_page == $page)
                );
            }
            // Last/Next Page
            if (($total_results - ($page * $page_limit)) > 0) {
                $pagenext = $page + 1;
                if ($numofpages > $endpage) {
                    $pagination['links'][] = array(
                            'url' => $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array($pagination_key => ceil($numofpages))))) . $anchor,
                            'link' => ceil($numofpages)
                    );
                }
                $pagination['next'] = array(
                        'url' => $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array($pagination_key => $pagenext)))) . $anchor,
                        'link' => '&gt;&gt;'
                );
            }
        }
        return $pagination;
    }
    
    /* Build the "SQL Limit" portion of the query based on the page limits and pagination information
	 * @param int $results_limit
	 * @param int $page_limit
	 * @param int $current_page
	 * @return string
	 */
    
    public static function buildSqlLimit($results_limit, $page_limit, $current_page)
    {
        $sql_limit = '';
        if ($results_limit > $page_limit) {
            $limitvalue = ($current_page * $page_limit) - $page_limit;
            $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
            $true_limit = ($limitvalue + $page_limit) > $results_limit ? ($results_limit - $limitvalue) : $page_limit;
            $sql_limit  = " LIMIT " . $limitvalue . "," . $true_limit;
        }
        return $sql_limit;
    }
    
    /**
     * Generate URL-Friendly Link from String
     *
     * @param string $input
     * @param string $regxp
     * @param boolean $lowercase
     * @return string
     */
    public static function slugify($input, $regxp = '/[^a-zA-Z0-9_-]/', $lowercase = true)
    {
        $output = str_replace(' ', '-', $input);
        $output = preg_replace($regxp, '', $output);
        $output = preg_replace('/(-+)/', '-', $output);
        $output = trim($output, '- ');
        if (!empty($lowercase)) {
            $output = strtolower($output);
        }
        return $output;
    }
    
    /**
     * Returns possible sort options
     * @return array
     */
    public static function getSortOptions()
    {
        return array(
            'DESC-PriceFrom-Subdivision',
            'ASC-PriceFrom-Subdivision',
            'ASC-SubdivisionName-Subdivision',
            'DESC-BasePrice-Listing',
            'ASC-BasePrice-Listing'
        );
    }
}
