<?php

function snippet_title($base = '')
{

    $price_ranges = get_price_range($_REQUEST['price_range']);

    $current_page = !empty($_REQUEST['p']) ? $_REQUEST['p'] : 1;

    if (is_array($price_ranges)) {
        if (!empty($price_ranges['maximum_price']) && empty($price_ranges['minimum_price'])) {
            $price_range = 'Under $' . number_format($price_ranges['maximum_price']);
        }
        if (!empty($price_ranges['minimum_price']) && !empty($price_ranges['maximum_price'])) {
            $price_range = '$' . number_format($price_ranges['minimum_price']) . ' - $' . number_format($price_ranges['maximum_price']);
        }
        if (!empty($price_ranges['minimum_price']) && empty($price_ranges['maximum_price'])) {
            $price_range = 'Over $' . number_format($price_ranges['minimum_price']);
        }
    }
    $snippet_title = $base . ', ' . (($price_range != '') ? $price_range . ', ' : '')/* . (($current_page != 1) ? ' Page ' . $current_page : '')*/;
    $snippet_title = trim($snippet_title, ', ');
    return $snippet_title;
}

function price_range_table($sql_mapping = '', $sql_extra = '')
{
    global $page;

    // URL Path
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Homepage URL
    if (empty($path)) {
        $path = '/';

        // CMS Page URL
    } elseif (preg_match('/\.php$/', $path)) {
        $path = '/' . str_replace('.php', '', $path) . '/';

        // Price Range URL
    } else {
        list ($name, $range) = explode('/', $path, 2);
        $path = !empty($range) ? '/' . $name . '/' : '/';
    }

    $column = 0;
    $columnCnt = 3;

    /* Include Request Mod */
    include DIR_INCLUDE . 'pages/search_request_mod.php';
    $output .= '<table style="width: 100%;" class="pricing-table">' . "\n";
    $price_ranges = get_price_ranges();
    foreach ($price_ranges as $price_range) {
        global $idx, $db_idx;
        $search_sql  = '';

        if (!empty($price_range['minimum_price'])) {
            if ($price_range['minimum_price'] >= $_REQUEST['snip_maximum_price'] && !empty($_REQUEST['snip_maximum_price'])) {
                continue;
            }
            $search_sql .= $db_idx->buildQueryString($idx->field('ListingPrice'), $price_range['minimum_price'], 'morethan');
        }
        if (!empty($price_range['maximum_price'])) {
            if ($price_range['maximum_price'] <= $_REQUEST['snip_minimum_price'] && !empty($_REQUEST['snip_minimum_price'])) {
                continue;
            }
            $search_sql .= $db_idx->buildQueryString($idx->field('ListingPrice'), $price_range['maximum_price'], 'lessthan');
        }

        /* Filtering out price because we are manually doing it above. */
        $searchFields = search_fields($idx, 'ListingPrice', true);
        $build_where = $idx->buildWhere($idx, $db_idx, 't1', $searchFields);
        $search_sql .= $build_where['search_where'];

        $search_sql  = (!empty($search_sql)) ? ' WHERE ' . rtrim($search_sql, "AND ") : '';

        if (!empty($sql_extra)) {
            $search_sql .= ' AND ' . $sql_extra;
        }

        if (!empty($sql_mapping)) {
            $search_sql .= " AND `t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL AND " . $sql_mapping;
            $count_results = $db_idx->fetchQuery("SELECT SQL_CACHE `t1`.`" . $idx->field('ListingMLS') . "`, `t2`.`Point` as Point FROM " . $idx->getTable() . " t1 JOIN `" . $idx->getTable('geo') . "` `t2` ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`" . $search_sql . " LIMIT 1") or print ($db_idx->error());
        } else {
            $count_results = $db_idx->fetchQuery("SELECT SQL_CACHE `t1`.`" . $idx->field('ListingMLS') . "` FROM " . $idx->getTable() . " `t1`" . $search_sql . " LIMIT 1") or print ($db_idx->error());
        }

        if (!empty($count_results)) {
            if (($column % $columnCnt) == 0) {
                $output .= ' <tr>' . "\n";
            }

            if (empty($price_range['maximum_price']) && empty($price_range['minimum_price'])) {
                $link_title = 'All Listings';
            }

            if (!empty($price_range['maximum_price']) && empty($price_range['minimum_price'])) {
                $link_title = 'Under $' . number_format($price_range['maximum_price']);
            }

            if (!empty($price_range['minimum_price']) && !empty($price_range['maximum_price'])) {
                $link_title = '$' . number_format($price_range['minimum_price']) . ' - $' . number_format($price_range['maximum_price']);
            }

            if (!empty($price_range['minimum_price']) && empty($price_range['maximum_price'])) {
                $link_title = 'Over $' . number_format($price_range['minimum_price']);
            }
            //if (($price_range['minimum_price'] == $_REQUEST['minimum_price']) && ($price_range['maximum_price'] == $_REQUEST['maximum_price'])) {
            if ($price_range['link'] == $_REQUEST['price_range']) {
                $output .= '  <td><b>' . $link_title . '</b></td>' . "\n";
            } else {
                $price_range['link'] = !empty($price_range['link']) ? $price_range['link'] . '/' : '';
                if (empty($price_range['link']) && strlen($path) > 1) {
                    $price_range['link'] = rtrim($path, '/') . '.php';
                } else {
                    $price_range['link'] = $path . $price_range['link'];
                }
                $output .= '  <td><a href="' . $price_range['link'] . '">' . $link_title . '</a></td>' . "\n";
            }

            if (($column % $columnCnt) == ($columnCnt - 1)) {
                $output .= ' </tr>' . "\n";
            }
            $column++;

            $count_results = false;
        }
    }
    if (($column % $columnCnt) > 0) {
        $output .= str_repeat('<td></td>', $columnCnt - ($column % $columnCnt)) . '</tr>' . "\n";
    }
    $output .= '</table>' . "\n";
    return $output;
}

function get_price_range($link_name)
{
    $price_ranges = get_price_ranges();
    foreach ($price_ranges as $price_range) {
        if ($price_range['link'] == $link_name) {
            return $price_range;
        }
    }
}

function get_price_ranges()
{
    $price_ranges   = array();
    $price_ranges[] = array ('link' => ''); // all listings
    $price_ranges[] = array ('link' => 'under-100000', 'minimum_price'=> 0, 'maximum_price' => 100000);
    $price_ranges[] = array ('link' => '100000-200000', 'minimum_price' => 100000, 'maximum_price' => 200000);
    $price_ranges[] = array ('link' => '200000-300000', 'minimum_price' => 200000, 'maximum_price' => 300000);
    $price_ranges[] = array ('link' => '300000-400000', 'minimum_price' => 300000, 'maximum_price' => 400000);
    $price_ranges[] = array ('link' => '400000-500000', 'minimum_price' => 400000, 'maximum_price' => 500000);
    $price_ranges[] = array ('link' => '500000-600000', 'minimum_price' => 500000, 'maximum_price' => 600000);
    $price_ranges[] = array ('link' => '600000-700000', 'minimum_price' => 600000, 'maximum_price' => 700000);
    $price_ranges[] = array ('link' => '700000-800000', 'minimum_price' => 700000, 'maximum_price' => 800000);
    $price_ranges[] = array ('link' => '800000-900000', 'minimum_price' => 800000, 'maximum_price' => 900000);
    $price_ranges[] = array ('link' => '900000-1000000', 'minimum_price' => 900000, 'maximum_price' => 1000000);
    $price_ranges[] = array ('link' => 'over-1000000', 'minimum_price' => 1000000, 'maximum_price' => 0);
    return $price_ranges;
}
