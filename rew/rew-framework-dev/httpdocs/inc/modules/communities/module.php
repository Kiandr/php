<?php

use REW\Backend\Partner\Inrix\DriveTime;
use REW\Core\Interfaces\LogInterface;

// Limit (Default: 3)
$limit = isset($this->config['limit']) && !empty($this->config['limit']) ? $this->config['limit'] : false;

// Allow HTML (Default: false)
$html = isset($this->config['html']) ? !empty($this->config['html']) : false;

// Truncate (Default: 500)
$default_limit = Skin::hasFeature(Skin::COMMUNITY_DESCRIPTION_NO_LIMIT) ? false : 500;
$truncate = isset($this->config['truncate']) ? (is_int($this->config['truncate']) ? $this->config['truncate'] : false) : $default_limit;

// Thumbnail Size
$thumbnails = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : '416x284';

// Placeholder Image
$placeholder = !empty($this->config['placeholder']) ? $this->config['placeholder'] : '/img/blank.gif';
$placeholder = !empty($thumbnails) ? '/thumbs/' . $thumbnails . $placeholder : $placeholder;

// Load onyl communities with an image
$hasImage = isset($this->config['hasImage']) ? $this->config['hasImage'] : false;

// Number of Thumbnails to Load (Set to -1 for all available)
$loadImages = isset($this->config['loadImages']) ? $this->config['loadImages'] : true;

// Load Community Statistics (# of Listings, AVG Price, MIN Price, MAX Price)
$loadStats = isset($this->config['loadStats']) ? $this->config['loadStats'] : true;

// Load Additional Statistics (AVG Beds, AVG Baths, AVG SqFt, AVG Acres)
$loadExtra = isset($this->config['loadExtra']) ? $this->config['loadExtra'] : false;

// Load Community Search Results (Set this to the # of results to load)
$loadResults = !empty($this->config['loadResults']) ? (int) $this->config['loadResults'] : false;

// Load contained areas (Set this to the # of results to load)
$loadContainedAreas = isset($this->config['loadContainedAreas']) ? $this->config['loadContainedAreas'] : false;

// Load Community's Tags & Keywords
$loadTags = !empty($this->config['loadTags']) ? true : false;

// Build IDX Search URL based on Community's Search Criteria
$searchUrl = isset($this->config['searchUrl']) ? $this->config['searchUrl'] : false;

// Sort communities
$orderBy = isset($this->config['orderBy']) ? $this->config['orderBy'] : false;
$orderBy = in_array($orderBy, array('order', 'title')) ? $orderBy : 'order';

// CMS Database
$db = DB::get('cms');

// SQL query parts
$sql_where = ["`is_enabled` = 'Y'"];
$sql_params = array();
$sql_select = array();

// Order
$sql_order = "`" . $orderBy . "` ASC";

// Featured Community ID
if (is_numeric($this->config['mode'])) {
    $sql_where = ["`id` = ?"];
    $sql_params[] = $this->config['mode'];
    if (empty($limit)) {
        $limit = 1;
    }

// Featured Community Snippet
} elseif (is_string($this->config['mode']) && !in_array($this->config['mode'], array('all', 'featured'))) {
    $sql_where[] = "`snippet` = ?";
    $sql_params[] = $this->config['mode'];
    $limit = 1;

// Featured Spotlight
} elseif (is_string($this->config['mode']) && $this->config['mode'] == 'featured') {
    $sql_order = "RAND()";
}

// Require an image
if (!empty($hasImage)) {
    $sql_where[] = "`id` IN (SELECT `row` FROM `cms_uploads` WHERE `type` = 'community' AND `row` IS NOT NULL)";
}

// SQL SELECT
$sql_select[] = "`id`, `title`, `subtitle`, `description`, `page_id`";
$sql_select[] = $loadStats ? "`stats_heading`, `stats_total`, `stats_average`, `stats_highest`, `stats_lowest`, `anchor_one_text`, `anchor_one_link`, `anchor_two_text`, `anchor_two_link`" : "";
$sql_select[] = $searchUrl || $loadStats ? "`idx_snippet`, `search_idx`, `search_criteria`" : "";
if (Skin::hasFeature(Skin::COMMUNITY_VIDEO_LINKS)) {
    $sql_select[] = "`video_link`";
}

$sql_select = implode(', ', array_filter($sql_select));

// SQL Limit
$sql_limit = !empty($limit) ? ' LIMIT ' . (int) $limit : '';

// SQL Where
$sql_where = !empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '';

// SQL Order
$sql_order = !empty($sql_order) ? ' ORDER BY ' . $sql_order : '';

try {
    // List of communities
    $communities = array();

    // Fetch communities from database
    $query = $db->prepare("SELECT " . $sql_select . " FROM `featured_communities`" . $sql_where . $sql_order . $sql_limit . ";");
    $query->execute($sql_params);
    $results = $query->fetchAll();
    foreach ($results as $community) {
        // Find community's assigned page
        if (!empty($community['page_id']) && is_numeric($community['page_id'])) {
            try {
                $link = $db->fetch("SELECT `file_name` FROM `pages` WHERE `page_id` = " . $db->quote($community['page_id']) . " LIMIT 1;");
                if (!empty($link)) {
                    $community['url'] = '/' . $link['file_name'] . '.php';
                }
            } catch (Exception $e) {
                Log::error($e);
            }
        }

        try {
            // Load community's photos
            $limit = $loadImages < 0 || $loadImages === true ? '' : ' LIMIT ' . $loadImages;
            $community['images'] = $db->fetchAll("SELECT `id`, `file` FROM `cms_uploads` WHERE `type` = 'community' AND `row` = " . $db->quote($community['id']) . " ORDER BY `order` ASC" . $limit . ";");
            $community['images'] = array_map(function ($image) use ($thumbnails) {
                if (empty($thumbnails)) {
                    return '/uploads/' . $image['file'];
                }
                return '/thumbs/' . $thumbnails . '/uploads/' . $image['file'];
            }, $community['images']);
        } catch (Exception $e) {
            Log::error($e);
        }

        try {
            // Load community's tags
            if (!empty($loadTags)) {
                $query = $db->prepare("SELECT `tag_name` FROM `featured_communities_tags` WHERE `community_id` = :community_id ORDER BY `tag_order` ASC;");
                $query->execute(array('community_id' => $community['id']));
                $community['tags'] = $query->fetchAll(PDO::FETCH_COLUMN);
            }
        } catch (Exception $e) {
            Log::error($e);
        }

        // 404 Image
        if (empty($community['images'])) {
            if (!empty($thumbnails)) {
                $community['images'] = array('/thumbs/' . $thumbnails . '/img/404.gif');
            } else {
                $community['images'] = array('/img/404.gif');
            }
        }

        // Community Photo
        $image = array_slice($community['images'], 0, 1);
        if (!empty($image)) {
            $community['image'] = $image['0'];
        }

        // Truncate Description
        if (!empty($truncate)) {
            $community['description'] = Format::truncate($community['description'], $truncate, '&hellip;', true, !empty($html));
        }

        // New Lines
        $community['description'] = nl2br(trim($community['description'], "\r\n "));

        // Strip HTML
        if (empty($html)) {
            $community['description'] = Format::stripTags($community['description']);
        }

        try {
            // Process search criteria
            if ($searchUrl || $loadStats) {
                // IDX Feed
                $community['search_idx'] = !empty($community['search_idx']) ? $community['search_idx'] : Settings::getInstance()->IDX_FEED;

                // IDX Feed
                $idx = Util_IDX::getIdx($community['search_idx']);
                $db_idx = Util_IDX::getDatabase($community['search_idx']);

                // Community search criteria
                $sql_where = array();
                $search_query = array();

                if (!empty($community['idx_snippet'])) {
                    $query = $db->prepare("SELECT `code` FROM `snippets` WHERE `type` = 'idx' AND `agent` <=> :agent AND `team` <=> :team AND `id` = :idx_snippet LIMIT 1;");
                    $query->execute(array(
                        'agent'       => Settings::getInstance()->SETTINGS['agent'],
                        'team'        => Settings::getInstance()->SETTINGS['team'],
                        'idx_snippet' => $community['idx_snippet']
                    ));
                    $community['search_criteria'] = $query->fetch(PDO::FETCH_COLUMN);
                }

                $community['search_criteria'] = !empty($community['search_criteria']) ? unserialize($community['search_criteria']) : array();
                if (!empty($community['search_criteria'])) {
                    $__REQUEST = $_REQUEST;
                    $_REQUEST = $community['search_criteria'];
                    $search_query = $idx->buildWhere($idx, $db_idx, 't1');
                    $search_where = $search_query['search_where'];

                    // Build and Set DriveTime Polygon
                    if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
                        $container = \Container::getInstance();
                        $log = $container->make(LogInterface::class);
                        $drive_time = $container->make(DriveTime::class);
                        try {
                            $drive_time->modifyServerMapRequests(
                                $_REQUEST['dt_address'],
                                $_REQUEST['dt_direction'],
                                $_REQUEST['dt_travel_duration'],
                                $_REQUEST['dt_arrival_time'],
                                $_REQUEST['place_zoom'],
                                $_REQUEST['place_lat'],
                                $_REQUEST['place_lng']
                            );
                        } catch (Exception $e) {
                            $log->error($e->getMessage());
                        }
                    }

                    /**
                     * Map Queries
                     */

                    // WHERE Queries
                    $search_where = !empty($search_where) ? array($search_where) : array();

                    // HAVING Queries
                    $search_having = array();

                    // Search Group
                    $search_group = array();

                    // Latitude / Longitude Columns
                    $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
                    $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

                    // Search in Bounds
                    if (!empty($_REQUEST['map']['bounds']) && Settings::getInstance()->IDX_FEED != 'cms') {
                        $bounds = $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'], $search_group, $col_latitude, $col_longitude);
                    }

                    // Search in Radiuses
                    $radiuses = $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);

                    // Search in Polygons
                    $polygons = $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
                    if (!empty($polygons)) {
                        $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
                    }

                    // Add to Search Criteria
                    $sql_mapping = false;
                    if (!empty($search_group)) {
                        $sql_mapping = '(' . implode(' OR ', $search_group) . ')';
                        $search_where[] = $sql_mapping;
                        if (!empty($search_having)) {
                            $sql_mapping .= ' HAVING ' . implode(' OR ', $search_having);
                        }
                    }

                    // Require mapping data
                    $mapping = !empty($polygons);

                    // Generate search URL from search criteria
                    $community['search_url'] = Settings::getInstance()->SETTINGS['URL_IDX'] . '?refine=true&feed=' . $idx->getLink();
                    if (!empty($_REQUEST)) {
                        // Searchable IDX Fields
                        $searchable = array_merge(DriveTime::IDX_FORM_FIELDS, array_map(function ($field) {
                            return $field['form_field'];
                        }, search_fields($idx)), array('map', 'search_location'));

                        // Current Search Criteria
                        $criteria = array();
                        array_walk($_REQUEST, function ($v, $k) use (&$criteria, $searchable) {
                            if (!empty($v) && in_array($k, $searchable)) {
                                $criteria[$k] = $v;
                            }
                        });

                        $search_url = http_build_query($criteria);
                        if (!empty($search_url)) {
                            $community['search_url'] .= '&' . $search_url;
                        }
                    }

                    $_REQUEST = $__REQUEST;
                } else {
                    // Any global criteria
                    $idx->executeSearchWhereCallback($search_where);
                }

                // Load community listings
                $community['listings'] = array();
                if (is_int($loadResults) && $loadResults > 0) {
                    // Load dismissed listings
                    $user = User_Session::get();
                    $dismissed = $user->getDismissedListings($idx);

                    // Exclude dismissed listings
                    if (!empty($dismissed)) {
                        $search_where[] = "`t1`.`ListingMLS` NOT IN ('" . implode("', '", $dismissed) . "')";
                    }

                    // Query String (WHERE & HAVING)
                    $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

                    // Order / Sort
                    if (!empty($community['search_criteria']['sort_by'])) {
                        list ($direction, $order_by) = explode('-', $community['search_criteria']['sort_by'], 2);
                        $order = '`' . $order_by . '` ' . $direction;
                    } else {
                        $order = '`ListingPrice` DESC';
                    }

                    // Load community search results
                    $search_results = $db_idx->query("SELECT " . $idx->selectColumns() . " FROM `" . $idx->getTable() . "`"
                        . " JOIN (SELECT `t1`.`id`"
                        . ($mapping ? ", `t2`.`Point`" : "")
                        . " FROM `" . $idx->getTable() . "` `t1`"
                        . ($mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "")
                        . (!empty($search_where) ? " WHERE " . $search_where : '')
                        . ') p USING(`id`)'
                        . " ORDER BY " . $order
                        . " LIMIT " . $loadResults);
                    while ($search_result = $db_idx->fetchArray($search_results)) {
                        $community['listings'][] = Util_IDX::parseListing($idx, $db_idx, $search_result);
                    }

                    // Listings were found!
                    if (!empty($community['listings'])) {
                        // Locate search result template
                        $page = $this->getContainer()->getPage();
                        $result_tpl = $page->locateTemplate('idx', 'misc', 'result');

                        // Load saved favorites
                        $bookmarked = $user->getSavedListings($idx);
                    }
                }

                // Load search stats
                if (!empty($loadStats)) {
                    // Community Defaults
                    $community['stats_heading']   = !empty($community['stats_heading'])   ? $community['stats_heading']   : 'Real Estate Statistics';
                    $community['stats_total']     = !empty($community['stats_total'])     ? $community['stats_total']     : 'Total Listings';
                    $community['stats_average']   = !empty($community['stats_average'])   ? $community['stats_average']   : 'Average Price';
                    $community['stats_highest']   = !empty($community['stats_highest'])   ? $community['stats_highest']   : 'Highest Price';
                    $community['stats_lowest']    = !empty($community['stats_lowest'])    ? $community['stats_lowest']    : 'Lowest Price';
                    $community['anchor_one_text'] = !empty($community['anchor_one_text']) ? $community['anchor_one_text'] : 'Community Summary';
                    $community['anchor_one_link'] = !empty($community['anchor_one_link']) ? $community['anchor_one_link'] : '#community-summary';
                    $community['anchor_two_text'] = !empty($community['anchor_two_text']) ? $community['anchor_two_text'] : 'Homes for Sale';
                    $community['anchor_two_link'] = !empty($community['anchor_two_link']) ? $community['anchor_two_link'] : '#homes-for-sale';

                    // Query String (WHERE & HAVING)
                    if (is_array($search_where)) {
                        $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');
                    }

                    // Statistics Query
                    $query = "SELECT SQL_CACHE "
                        . "COUNT(*) AS `total`, "
                        . "ROUND(AVG(`" . $idx->field('ListingPrice') . "`)) AS `average`, "
                        . "MAX(`" . $idx->field('ListingPrice') . "`) AS `max`, "
                        . "MIN(`" . $idx->field('ListingPrice') . "`) AS `min` "
                        . ($loadExtra ? ", ROUND(AVG(`" . $idx->field('NumberOfBedrooms') . "`)) AS `beds` "
                            // Average property stats
                            . ", ROUND(AVG(`" . $idx->field('NumberOfBathrooms') . "`)) AS `baths` "
                            . ", ROUND(AVG(`" . $idx->field('NumberOfSqFt') . "`)) AS `sqft` "
                            . ", ROUND(AVG(`" . $idx->field('NumberOfAcres') . "`), 2) AS `acres` "
                            . ", ROUND(AVG(`" . $idx->field('YearBuilt') . "`)) AS `built` "
                            // Price per square foot
                            . ", ROUND(AVG(`" . $idx->field('ListingPrice') . "` / `" . $idx->field('NumberOfSqFt') . "`)) AS `avg_price_sqft`"
                            . ", ROUND(MAX(`" . $idx->field('ListingPrice') . "`/`" . $idx->field('NumberOfSqFt') . "`)) AS `max_price_sqft`"
                            . ", ROUND(MIN(`" . $idx->field('ListingPrice') . "`/`" . $idx->field('NumberOfSqFt') . "`)) AS `min_price_sqft`"
                        : "")
                        . " FROM `" . $idx->getTable() . "`"
                        . " JOIN (SELECT `t1`.`id`"
                        . ($mapping ? ", `t2`.`Point`" : "")
                        . " FROM `" . $idx->getTable() . "` `t1`"
                        . ($mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "")
                        . (!empty($search_where) ? " WHERE " . $search_where : '')
                        . ') p USING(`id`)'
                    . ";";

                    // Check Memcache
                    $index = $this->getID() . ':' . md5($query);
                    $cached = Cache::getCache($index);
                    if (!is_null($cached)) {
                        // Use Cache
                        $community['stats'] = $cached;
                    } else {
                        // Fetch Statistics
                        $community['stats'] = $db_idx->fetchQuery($query);

                        // Save Cache
                        Cache::setCache($index, $community['stats']);
                    }
                }

                if ($loadContainedAreas) {
                    $query = 'SELECT ' . $idx->selectColumns(null, array('AddressArea')) . ', COUNT(*) AS `count` FROM `' . $idx->getTable() . '` t1'
                        . ' WHERE ' . ($search_where ? $search_where . ' AND ' : '') .' `AddressArea` != "" AND `AddressArea` IS NOT NULL '
                        . ' GROUP BY `AddressArea` '
                        . ' ORDER BY `count` DESC '
                        . ' LIMIT ' . ((int) $loadContainedAreas);
                    $index = $this->getID() . ':' . md5($query);
                    $cached = Cache::getCache($index);
                    if (!is_null($cached)) {
                        // Use Cache
                        $community['areas'] = $cached;
                    } else {
                        $search_results = $db_idx->query($query);

                        while ($search_result = $db_idx->fetchArray($search_results)) {
                            $area = ucwords(strtolower($search_result['AddressArea']));
                            if (!isset($community['areas'][$area])) {
                                $community['areas'][$area] = 0;
                            }
                            $community['areas'][$area] += $search_result['count'];
                        }

                        Cache::setCache($index, $community['areas']);
                    }
                }
            }

        // Error Occurred
        } catch (Exception $e) {
            Log::error($e);
        }

        // Add to list of communities
        $communities[] = $community;
    }

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
