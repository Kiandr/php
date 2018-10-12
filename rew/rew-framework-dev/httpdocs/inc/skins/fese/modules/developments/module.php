<?php

// Require installation of REW Developments Module
if (empty(Settings::getInstance()->MODULES['REW_DEVELOPMENTS'])) {
    return;
}

// Config: Load matching IDX listings (default: false)
$config_listings = !empty($this->config['listings']) ? (int) $this->config['listings'] : false;

// Config: Thumbnail size (default: 500x500)
$config_thumbs = isset($this->config['thumbnails']) ? $this->config['thumbnails'] : '500x500';

// Config: # of images to load (default: 1)
$config_images = isset($this->config['images']) ? $this->config['images'] : 1;

// Config: # of records to load (default: 0)
$config_limit = isset($this->config['limit']) && !empty($this->config['limit']) ? $this->config['limit'] : 0;

// Config: Sort order (default: order)
$config_order = isset($this->config['order']) ? $this->config['order'] : false;
$config_order = in_array($config_order, ['title', 'RAND()', 'order']) ? $config_order : 'order';
$config_order = $config_order === 'RAND()' ? $config_order : sprintf('`%s`', $config_order);

// Config: Placeholder image (default: /img/404.gif)
$config_placeholder = $this->config['placeholder'] ?: '/img/404.gif';
$placeholder = ($config_thumbs ? sprintf('/thumbs/%s', $config_thumbs) : '') . $config_placeholder;

// Config: Only show featured developments
$config_featured = !empty($this->config['featured']);

// Config: Exclude development with id
$config_exclude = !empty($this->config['exclude']) ? $this->config['exclude'] : false;
$config_exclude = is_int($config_exclude) ? $config_exclude : false;

// Config: Selected development fields
$config_select = "`id`, `link`, `title`, `subtitle`, `state`, `unit_min_price`, `unit_max_price`";
// Selecting `search_criteria` will load matching IDX listings
// Selecting `community_id` will load community and images

// Config: Development details
$config_details = $this->config['details'] ?: null;
if (!empty($config_details)) {
    $config_images = true;
    $config_select = '*';
    $config_limit = 1;
    $config_order = 0;
}

// Developments list
$developments = [];

// Database connection
$db = DB::get('cms');

// Load available developments from database
$query = $db->prepare(
    sprintf("SELECT %s FROM `developments`", $config_select)
    . " WHERE `is_enabled` = 'Y'"
    . ($config_exclude ? ' AND `id` != ?' : '')
    . ($config_featured ? " AND `is_featured` = 'Y'" : '')
    . ($config_details ? sprintf(' AND `id` = %d', $config_details) : '')
    . ($config_order ? sprintf(' ORDER BY %s', $config_order) : '')
    . ($config_limit ? sprintf(' LIMIT %d', $config_limit) : '')
    . ";"
);
$query->execute([$config_exclude]);
$results = $query->fetchAll();
if (!empty($results)) {
    // Prepare DB query to fetch development photos
    $find_development_photos = $db->prepare(sprintf(
        "SELECT `file` FROM `%s` WHERE `type` = 'development' AND `row` = ? ORDER BY `order` ASC%s;",
        Settings::getInstance()->TABLES['UPLOADS'],
        (is_int($config_images) ? sprintf(" LIMIT %d", $config_images) : '')
    ));

    // Prepare DB query to fetch community photos
    $find_community_photos = $db->prepare(sprintf(
        "SELECT `file` FROM `%s` WHERE `type` = 'community' AND `row` = ? ORDER BY `order` ASC;",
        Settings::getInstance()->TABLES['UPLOADS']
    ));

    // Prepare DB query to fetch development tags
    $find_development_tags = $db->prepare("SELECT `tag_name` FROM `developments_tags` WHERE `development_id` = ? ORDER BY `tag_order` ASC;");

    // Prepare DB query to find development community
    $find_development_community = $db->prepare("SELECT `id`, `title`, `description` FROM `featured_communities` WHERE `id` = ? LIMIT 1;");

    // Prepare DB query to find IDX snippet for development
    if (!empty($config_listings)) {
        $find_development_snippet = $db->prepare("SELECT `code` FROM `snippets` WHERE `type` = 'idx' AND `agent` = ? AND `id` = ? LIMIT 1;");
    }

    // Load additional development data
    foreach ($results as $development) {
        // Development's page URL
        $development['url'] = sprintf('/development/%s/', $development['link']);

        // Load development's photos
        $development['image'] = null;
        $development['images'] = [];
        if (!empty($config_images)) {
            $find_development_photos->execute([$development['id']]);
            while ($image = $find_development_photos->fetchColumn()) {
                $image = $config_thumbs
                    ? sprintf('/thumbs/%s/uploads/%s', $config_thumbs, $image)
                    : sprintf('/uploads/%s', $image);
                $development['images'][] = $image;
                if (empty($development['image'])) {
                    $development['image'] = $image;
                }
            }
        }

        // Load development's tags
        $find_development_tags->execute([$development['id']]);
        if ($tags = $find_development_tags->fetchAll(\PDO::FETCH_COLUMN)) {
            $development['tags'] = $tags;
        }

        // Load development's community
        $development['community'] = null;
        if (!empty($development['community_id'])) {
            $find_development_community->execute([$development['community_id']]);
            if ($community = $find_development_community->fetch()) {
                // Load community's photos
                $find_community_photos->execute([$community['id']]);
                while ($image = $find_community_photos->fetchColumn()) {
                    $image = sprintf('/uploads/%s', $image);
                    $community['images'][] = $image;
                    if (empty($community['image'])) {
                        $community['image'] = $image;
                    }
                }

                // Add development's community details
                $development['community'] = $community;
            }
        }

        // Find matching IDX search results
        if (!empty($config_listings)) {
            $development['listings'] = [];
            if ($development['idx_listings'] === 'Y') {
                // Use criteria from IDX snippet
                $search_criteria = $development['idx_criteria'];
                if (!empty($development['idx_snippet_id'])) {
                    $find_development_snippet->execute([
                        // TODO: use $development['agent_id'] ???
                        Settings::getInstance()->SETTINGS['agent'],
                        $development['idx_snippet_id']
                    ]);
                    $search_criteria = $find_development_snippet->fetchColumn();
                }

                // Unserialize search criteria
                $search_criteria = $search_criteria ? unserialize($search_criteria) : [];
                $search_criteria = is_array($search_criteria) ? $search_criteria : [];
                if (!empty($search_criteria)) {
                    // IDX feed to use
                    $feed = $search_criteria['idx'] ?: $development['idx_feed'] ?: null;

                    // IDX Feed
                    $idx = Util_IDX::getIdx($feed);
                    $db_idx = Util_IDX::getDatabase($feed);

                    // Build search query
                    $search_where = [];
                    $__REQUEST = $_REQUEST;
                    $_REQUEST = $search_criteria;
                    $search_query = $idx->buildWhere($idx, $db_idx, 't1');
                    if (!empty($search_query['search_where'])) {
                        $search_where[] = $search_query['search_where'];
                    }

                    // Map criteria
                    $search_having = [];
                    $map_criteria = $_REQUEST['map'];
                    if (!empty($map_criteria)) {
                        $search_group = [];
                        $col_lat = sprintf('`t1`.`%s`', $idx->field('Latitude'));
                        $col_lng = sprintf('`t1`.`%s`', $idx->field('Longitude'));
                        if (!empty($map_criteria['bounds'])) {
                            $idx->buildWhereBounds($map_criteria['ne'], $map_criteria['sw'], $search_group, $col_lat, $col_lng);
                        }
                        if (!empty($map_criteria['radius'])) {
                            $idx->buildWhereRadius($map_criteria['radius'], $search_group, $col_lat, $col_lng);
                        }
                        if (!empty($map_criteria['polygon'])) {
                            $polygons = $idx->buildWherePolygons($map_criteria['polygon'], $search_group, $search_having, 't2.Point');
                            if (!empty($polygons)) {
                                $search_where[] = sprintf('`t1`.`%s` IS NOT NULL', $idx->field('ListingMLS'));
                            }
                        }
                        if (!empty($search_group)) {
                            $search_where[] = '(' . implode(' OR ', $search_group) . ')';
                        }
                    }

                    // Any global criteria
                    $idx->executeSearchWhereCallback($search_where);

                    // Query String (WHERE & HAVING)
                    $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

                    // Load search results from IDX
                    $queryString = sprintf('SELECT %s FROM `%s`', $idx->selectColumns(), $idx->getTable())
                        . ' JOIN (SELECT `t1`.`id`'
                        . ($mapping ? ', `t2`.`Point`' : '')
                        . sprintf(' FROM `%s` `t1`', $idx->getTable())
                        . ($mapping ? " JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS` AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`" : "")
                        . ($mapping ? sprintf(' JOIN `%s` `t2` ON `t1`.`%s` = `t2`.`ListingMLS` AND `t1`.`%s` = `t2`.`ListingType`', $idx->getTable('geo'), $idx->field('ListingMLS'), $idx->field('ListingType')) : '')
                        . ($search_where ? sprintf(' WHERE %s', $search_where) : '')
                        . ') p USING(`id`) ORDER BY `id` DESC'
                        . ($config_listings ? sprintf(' LIMIT %d', $config_listings) : '');
                    $search_results = $db_idx->query($queryString);
                    while ($search_result = $db_idx->fetchArray($search_results)) {
                        $development['listings'][] = Util_IDX::parseListing($idx, $db_idx, $search_result);
                    }

                    // Search results found
                    if (!empty($development['listings'])) {
                        // Locate search result template
                        $page = $this->getContainer()->getPage();
                        $result_tpl = $page->locateTemplate('idx', 'misc', 'result');

                        // Load saved favorites
                        $user = User_Session::get();
                        $bookmarked = $user->getSavedListings($idx);
                    }
                }
            }
        }

        // Add to featured development list
        $developments[] = $development;
    }
}
