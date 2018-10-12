<?php

// Require Authorization

// Get Authorization Managers
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$reportsAuth->canViewListingReport($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view listing reports')
    );
}

    // Full Page
    $body_class = 'full';

    // Show Form
    $show_form = true;

    // Hide Report
    $report = false;

    // Errors
    $errors = array();

    // Filter by Date
    $sql_date = false;
    $start = false;
    $end = false;
if (!empty($_GET['start']) && !empty($_GET['end'])) {
    $start = strtotime($_GET['start']);
    $end = strtotime($_GET['end']);
    if (!empty($start) && !empty($end)) {
        $sql_date = "`%s` BETWEEN '" . date('Y-m-d 00:00:00', $start) . "' AND '" . date('Y-m-d 23:59:59', $end) . "'";
    }
}

    // Date Filters
    $ranges = array(
        array('title' => __('All Time'),        'value' => 'all', 'selected' => empty($sql_date)),
        array('title' => __('Last 7 Days'),     'value' => date('Y-m-d', strtotime('-7 days'))  . '|' . date('Y-m-d')),
        array('title' => __('Last 14 Days'),    'value' => date('Y-m-d', strtotime('-14 days')) . '|' . date('Y-m-d')),
        array('title' => __('Last 30 Days'),    'value' => date('Y-m-d', strtotime('-30 days')) . '|' . date('Y-m-d')),
        array('title' => __('Last 60 Days'),    'value' => date('Y-m-d', strtotime('-60 days')) . '|' . date('Y-m-d')),
        array('title' => __('Custom Range'),    'value' => 'custom', 'selected' => !empty($sql_date))
    );

    // Generate Report
    if (!empty($_GET['mls'])) {
        // IDX Feed & Database
        $idx = Util_IDX::getIdx();
        $db_idx = Util_IDX::getDatabase();

        $search_where = "`ListingMLS` = '" . $db_idx->cleanInput($_GET['mls']) . "'";

        // Any global criteria
        $idx->executeSearchWhereCallback($search_where);

        // Find Listing
        $listing = $db_idx->fetchQuery("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "` WHERE " . $search_where . " LIMIT 1;");
        if (!empty($listing)) {
            // Hide Form
            $show_form = false;

            // Full Details
            $listing = Util_IDX::parseListing($idx, $db_idx, $listing);

            // Agent Stats
            if (isset($_GET['ajax'])) {
                // Show Report
                $report = true;

                $db = DB::get();

                // Saved & Recommended
                $favorites = array();
                $recommended = array();

                try {
                    $query = "SELECT `user_id`, `agent_id`, DATE(`timestamp`) AS `date`"
                        . " FROM `users_listings`"
                        . " WHERE `idx` = '" . $idx->getName() . "' AND `mls_number` = :mls_number"
                        . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'timestamp') : '')
                        . " ORDER BY `timestamp` DESC"
                    . ";";

                    $result = $db->fetchAll($query, ["mls_number" => $listing['ListingMLS']]);
                    foreach ($result as $row) {
                        if (!empty($row['agent_id'])) {
                            $recommended[$row['date']] += 1;
                        } else {
                            $favorites[$row['date']] += 1;
                        }
                    }
                } catch (PDOException $e) {
                }

                // Inquiries
                $forms = array();
                try {
                    $query = "SELECT DATE(`timestamp`) AS `date`, `form`"
                        . " FROM `users_forms`"
                        . " WHERE `page` LIKE :page"
                        . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'timestamp') : '')
                        . " ORDER BY `timestamp` DESC"
                    . ";";
                    $result = $db->fetchAll($query, ["page" => $listing['url_details']."%"]);
                    foreach ($result as $row) {
                        // Add to Inquiries
                        $forms[$row['form']][$row['date']]++;
                    }
                } catch (PDOException $e) {
                }

                // Listing URLs
                $urls = array();
                foreach ($listing as $key => $url) {
                    if (preg_match("/^url_/", $key)) {
                        $urls[$key] = $url;
                    }
                }

                // Pages
                $pages = array();
                $referers = array();
                foreach ($urls as $key => $url) {
                    // URL Combinations
                    $matches = array();
                    foreach (array(
                        $url,
                        rtrim($url, '/')
                    ) as $u) {
                        $matches[] = $u;
                        switch ($key) {
                            case 'url_details':
                                $matches[] = $u . '?submit';
                                $matches[] = $u . '?inquire';
                                $matches[] = $u . '?comment';
                                $matches[] = $u . '?comments';
                                break;
                            case 'url_register':
                                $matches[] = $u . '?register';
                                break;
                            case 'url_inquire':
                                $matches[] = $u . '?submit';
                                $matches[] = $u . '?inquire_type=More+Info';
                                $matches[] = $u . '?inquire_type=Property+Showing';
                                break;
                            case 'url_onboard':
                                $matches[] = $u . '?view=community-information';
                                $matches[] = $u . '?view=nearby-amenities';
                                $matches[] = $u . '?view=nearby-schools';
                                break;
                        }
                    }

                    // Append Queries
                    $append = array('popup', 'facebox_Frame=true', 'facebox_Frame=false');
                    if ($key === 'url_details') {
                        $append[] = '#quick-inquire';
                        $append[] = '#quick-showing';
                    }

                    // Search by Hash
                    $sql_where = array();
                    foreach ($matches as $match) {
                        $q = (strpos($match, '?') === false ? '?' : '&');
                        $sql_where[] = "`p`.`hash` = UNHEX(MD5('" . $match . "'))";
                        $sql_where[] = "`p`.`hash` = UNHEX(MD5('" . $match . $q . "'))";
                        if (!empty($append)) {
                            foreach ($append as $query) {
                                $sql_where[] = "`p`.`hash` = UNHEX(MD5('" . $match . (strpos($query, '#') === 0 ? '' : $q) . $query . "'))";
                            }
                        }
                    }

                    // Search Query
                    $sql_where = "(" . implode(" OR ", $sql_where) . ")";

                    // Find Visits by Date
                    $query = "SELECT DATE(`pv`.`timestamp`) AS `date`, COUNT(`pv`.`id`) AS `views`"
                        . " FROM `users_pages` `p`"
                        . " LEFT JOIN `users_pageviews` `pv` ON `p`.`id` = `pv`.`page_id`"
                        . " LEFT JOIN `users_sessions` `s` ON `pv`.`session_id` = `s`.`id`"
                        . " WHERE "
                        . $sql_where
                        . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'pv`.`timestamp') : '')
                        . " AND `s`.`user_id` IS NOT NULL"
                        . " GROUP BY DATE(`timestamp`)"
                        . " HAVING `views` > 0"
                        . " ORDER BY `pv`.`timestamp` DESC"
                    . ";";

                    try {
                        $result = $db->fetchAll($query);
                        foreach ($result as $row) {
                            // Add to Pages
                            $pages[$key][$row['date']] = $row['views'];
                        }
                    } catch (PDOException $e) {
                        $errors[] = __('Error loading visits.');
                    }

                    // Find Referers for Page
                    $query = "SELECT `ref`.`url` AS `referer`, COUNT(`ref`.`id`) AS `total`"
                        . " FROM `users_pages` `p`"
                        . " LEFT JOIN `users_pageviews` `pv` ON `p`.`id` = `pv`.`page_id`"
                        . " LEFT JOIN `users_pages` `ref` ON `pv`.`referer_id` = `ref`.`id`"
                        . " LEFT JOIN `users_sessions` `s` ON `pv`.`session_id` = `s`.`id`"
                        . " WHERE "
                        . $sql_where
                        . (!empty($sql_date) ? ' AND ' . sprintf($sql_date, 'pv`.`timestamp') : '')
                        . " AND `s`.`user_id` IS NOT NULL"
                        . " GROUP BY `pv`.`referer_id`"
                        . " ORDER BY `total` DESC"
                    . ";";

                    try {
                        $result = $db->fetchAll($query);
                        foreach ($result as $row) {
                            if ($row['total'] == 0) {
                                continue;
                            }

                            // Referer Page
                            $referer = $row['referer'];
                            $parts = parse_url($referer);

                            // Internal Link
                            $domain = strtolower($_SERVER['SERVER_NAME']);
                            $referer = str_replace($parts['host'], strtolower($parts['host']), $referer);
                            $parts['host'] = strtolower($parts['host']);
                            if ($domain == $parts['host']) {
                                // Ignore Backend URLs
                                if (strpos($parts['path'], '/backend/') === 0) {
                                    continue;
                                }

                                // Remove Query from URL
                                $referer = str_replace('?' . $parts['query'], '', $referer);

                                // Listing URL
                                if (in_array($referer, $urls)) {
                                    continue;
                                }

                                // Remove Scheme & Domain from URL
                                $referer = str_replace($parts['scheme'] . '://' . $domain, '', $referer);

                            // External
                            } else {
                                $referer = $parts['host'];
                            }

                            // Add to Referers
                            $referers[$referer] += $row['total'];
                        }
                        arsort($referers);
                    } catch (PDOException $e) {
                        $errors[] = __('Error loading referers.');
                    }
                }

                // Chart Series
                $maxDate = 0;
                $minDate = null;
                $series = array();

                // Build Charts
                foreach (array(array(
                    'name'  => __('Viewed Pages'),
                    'type'  => 'area',
                    'color' => '#0077cc',
                    'data'  => $pages,
                    'fill'  => true
                ), array(
                    'name'  => __('Inquiries'),
                    'type'  => 'column',
                    'color' => '#019700',
                    'data'  => $forms
                ), array(
                    'name'  => Locale::spell('Favorites'),
                    'type'  => 'column',
                    'color' => '#9966cc',
                    'data'  => array($favorites)
                ), array(
                    'name'  => __('Recommended'),
                    'type'  => 'column',
                    'color' => '#ff6600',
                    'data'  => array($recommended)
                )) as $chart) {
                    // Chart Data
                    $data = array();
                    array_walk($chart['data'], function ($dates) use (&$data) {
                        foreach ($dates as $date => $count) {
                            $data[$date] += $count;
                        }
                    });

                    // No Data
                    if (empty($data)) {
                        continue;
                    }

                    // Atleast 14 Days of Data
                    if (!empty($chart['fill'])) {
                        $startDate = strtotime('-14 Days');
                        $endDate   = time();
                        while ($startDate <= $endDate) {
                            $date = date('Y-m-d', $startDate);
                            if (!isset($data[$date])) {
                                $data[$date] = 0;
                            }
                            $startDate += (1 * 24 * 3600);
                        }
                    }
                    ksort($data);

                    // Add Chart
                    $series[] = array(
                        'name'  => $chart['name'],
                        'type'  => $chart['type'],
                        'color' => $chart['color'],
                        'data'  => array_map(function ($v, $k) use (&$minDate, &$maxDate) {
                            $k = strtotime($k) * 1000;
                            $maxDate = ($maxDate < $k) ? $k : $maxDate;
                            $minDate = (is_null($minDate) || $minDate > $k) ? $k : $minDate;
                            return array($k, $v);
                        }, $data, array_keys($data)),
                        'pointInterval' => 24 * 3600 * 1000
                    );
                }

                // Add Pie Chart
                if (!empty($pages)) {
                    $pie = array(
                        'type'          => 'pie',
                        'name'          => 'Visits',
                        'data'          => array_map(function ($v, $k) {
                            $k = ucwords(str_replace('url_', '', $k));
                            $v = array_sum($v);
                            return array($k, $v);
                        }, $pages, array_keys($pages))
                    );
                }

                // Sort Forms
                uasort($forms, function ($a, $b) {
                    return array_sum($b) - array_sum($a);
                });

                // Sort Pages
                uasort($pages, function ($a, $b) {
                    return array_sum($b) - array_sum($a);
                });
            }

        // Listing Not Found
        } else {
            $errors[] = __('The selected MLS&reg; Listing could not be found.');
        }
    }
