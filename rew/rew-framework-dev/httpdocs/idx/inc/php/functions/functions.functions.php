<?php

/**
 *
 * Return pagination information
 *
 * @param int $total_results
 * @param int $page
 * @param int $page_limit
 * @param string $url
 * @param string $anchor
 * @param array $query_string
 * @return array
 */
function generate_page_bar($total_results, $page, $page_limit, $url = '', $anchor = '', $query_string = false)
{

    $page = (!empty($page)) ? $page : 1;
    $numofpages = ceil($total_results / $page_limit);
    $startpage = ($numofpages > 3) ? $page - 4 : "0";
    $startpage = ($startpage < 0) ? "0" : $startpage;
    $endpage = $numofpages;
    $endpage = ($numofpages > 3) ? $page + 3 : 3;
    $endpage = ($endpage > $numofpages) ? $numofpages : $endpage;

    /* Query String */
    if (!is_array($query_string)) {
        $query_string = array();
        if (strpos($_SERVER['REQUEST_URI'], '?')) {
            parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_string);
            // unset vars we dont want carried over
            unset($query_string['search_title']);
            unset($query_string['p']);
        }
    }

    /* Pagination Collection */
    $pagination = array();

    /* Pagination Information */
    $pagination['pages'] = $numofpages;
    $pagination['page'] = $page;

    /* Showing Results */
    $pagination['start'] = (($page * $page_limit) - $page_limit) + 1;
    $pagination['end'] = ($page * $page_limit);

    // Current URL
    $url = !empty($url) ? $url : Http_Uri::getUri();

    if ($numofpages > 1) {
        // First/Prev Page
        if ($page != 1) {
            $pageprev = $page - 1;
            if ($pageprev == 1) {
                $page_url = $url . (!empty($query_string) ? '?' . htmlspecialchars(http_build_query($query_string)) : '');
            } else {
                $page_url = $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => $pageprev))));
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
                $page_url = $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => $real_page))));
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
                    'url' => $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => ceil($numofpages))))) . $anchor,
                    'link' => ceil($numofpages)
                );
            }
            $pagination['next'] = array(
                'url' => $url . '?' . htmlspecialchars(http_build_query(array_merge($query_string, array('p' => $pagenext)))) . $anchor,
                'link' => '&gt;&gt;'
            );
        }
    }
    return $pagination;
}

function in_array_nocase($search, &$array)
{
    $search = strtolower($search);
    foreach ($array as $item) {
        if (strtolower($item) == $search) {
            return true;
        }
    }
    return false;
}

function requested_listing()
{

    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();

    // Subdomain Feed Validation Check
    if (((!empty(Settings::getInstance()->SETTINGS['agent'])
                && Settings::getInstance()->SETTINGS['agent'] != 1
                && !in_array($idx->getName(), Settings::getInstance()->SETTINGS['agent_idxs']))
         || (!empty(Settings::getInstance()->SETTINGS['team'])
                && !in_array($idx->getName(), Settings::getInstance()->SETTINGS['team_idxs'])))
        && !($idx->isCommingled())
        && $idx->getName() !== 'cms'
    ) {
        return null;
    }

    // CMS Listings
    if ($idx->getName() == 'cms' && !empty($_REQUEST['plink'])) {
        // Requested Link
        $link = $_REQUEST['plink'];

        $sql_where = '';

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Find Listing from Link
        $listing = $db_idx->fetchQuery("SELECT SQL_CACHE " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE `" . $idx->field('ListingLink') . "` = '" . $db_idx->cleanInput($link) . "' " . (!empty($sql_where) ? " AND " . $sql_where : "") . " LIMIT 1;");
        if (!empty($listing)) {
            return Util_IDX::parseListing($idx, $db_idx, $listing);

        // Find Listing from MLS Number
        } else {
            $listing = $db_idx->fetchQuery("SELECT SQL_CACHE " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE `" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($link) . "' " . (!empty($sql_where) ? " AND " . $sql_where : "") . " LIMIT 1;");
            if (!empty($listing)) {
                // Parse Listing
                $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

                // Return Listing
                if (empty($listing['ListingLink'])) {
                    return $listing;

                // Redirect to Permalink
                } else {
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $listing['url_details']);
                    exit;
                }
            }
        }

        // Listing Not Found
        return null;
    }

    $sql_where = '';

    // Find Listing by MLS #
    if (!empty($_REQUEST['pid'])) {
        $ListingLink = $_REQUEST['pid'];

        // Find Listing
        $sql_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($ListingLink) . "'";

    // Find Listing by Link
    } elseif (!empty($_REQUEST['plink'])) {
        $ListingLink = $_REQUEST['plink'];

        // Get MLS # from Listing Link
        list ($part1, $part2, $part3) = explode('-', $ListingLink);
        $_REQUEST['pid'] = $part1;

        // Find Listing
        $sql_where = "`" . $idx->field('ListingMLS') . "` IN ('" . $db_idx->cleanInput($part1) . "', '" . $db_idx->cleanInput($part1 . '-' . $part2) . "')";

    // Legacy 2.x URLs
    } elseif (!empty($_REQUEST['feed']) && preg_match('#\.html$#', Http_Uri::getUri())) {
        $ListingLink = $_REQUEST['feed'];

        // Find Listing
        $sql_where = "`" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($ListingLink) . "'";
    }

    // We Have The MLS Number!
    if (!empty($sql_where)) {
        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        // Find Listing
        $query = "SELECT SQL_CACHE " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $sql_where . " LIMIT 1;";

        // Find Listing by MLS Number
        $listing = $db_idx->fetchQuery($query);

        // Comingled Feed Agent Subdomain Feed Validation Check
        if (Settings::getInstance()->SETTINGS['agent'] != 1
            && !empty($listing['ListingFeed'])
            && !in_array($listing['ListingFeed'], Settings::getInstance()->SETTINGS['agent_idxs'])) {
                return null;
        }

        // If Co-Mingled Feed And The Listing's Source Feed Differs From What Is Currently Loaded For Compliance, Load The Source Feed.
        if (!empty($listing['ListingFeed']) && $listing['ListingFeed'] != Settings::getInstance()->IDX_FEED) {
            Util_IDX::switchFeed($listing['ListingFeed']);
        }

        // Listing Found
        if (!empty($listing)) {
            // Listing Details
            $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

            // Redirect to Actual Link
            if ($ListingLink !== $listing['ListingLink']) {
                $page = ($_GET['load_page'] == 'details') ? '' : (!empty($_GET['load_page']) ? $_GET['load_page'] . '/' : '');
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $listing['url_details'] . $page);
                exit;
            }

            // Return Listing
            return $listing;
        }
    }

    // Listing Not Found
    return null;
}
