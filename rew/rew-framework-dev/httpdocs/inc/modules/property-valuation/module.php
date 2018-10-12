<?php

// Global Resources
global $_COMPLIANCE;
$idx = Util_IDX::getIdx();
\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($idx->getName());

/**
 * @todo - ability to export cma_location data
 * @todo - support multiple idx feeds
 */

// Require REW_PROPERTY_VALUATION Module
if (empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) {
    unset($this->fileTemplate);
    unset($this->fileJavascript);
    unset($this->fileStylesheet);
    return;
}

// Treat this snippet like an IDX snippet (adds the IDX disclaimer on the
// bottom of the page if we aren't forcing the disclaimer to display directly below the listings)
if (empty($_COMPLIANCE['results']['show_immediately_below_listings'])) {
    $_REQUEST['snippet'] = true;
}

// User instance
$user = User_Session::get();

// Page Instance
$page = $this->getContainer()->getPage();

// Page variables over-ride config
$variables = $page->getVariables();
if (!empty($variables)) {
    $config = array_keys($this->getConfig());
    if (!empty($config)) {
        foreach ($config as $option) {
            if (isset($variables[$option])) {
                $this->config($option, $variables[$option]);
            }
        }
    }
}

// Load Map API
$page->getSkin()->loadMapApi();

// Load search criteria
$search = $this->config('search');
$searchCriteria = !empty($search) ? $search : array();

// Set search criteria
if (empty($search)) {
    $searchCriteria['feed']         = isset($_GET['feed'])      ? $_GET['feed']      : $this->config('defaults.feed')       ?: false;
    $searchCriteria['type']         = isset($_GET['type'])      ? $_GET['type']      : $this->config('defaults.type')       ?: false;
    $searchCriteria['subtype']      = isset($_GET['subtype'])   ? $_GET['subtype']   : $this->config('defaults.subtype')    ?: false;
    $searchCriteria['beds']         = isset($_GET['beds'])      ? $_GET['beds']      : $this->config('defaults.beds')       ?: 0;
    $searchCriteria['baths']        = isset($_GET['baths'])     ? $_GET['baths']     : $this->config('defaults.baths')      ?: 0;
    $searchCriteria['sqft']         = isset($_GET['sqft'])      ? $_GET['sqft']      : $this->config('defaults.sqft')       ?: 0;
    $searchCriteria['condition']    = isset($_GET['condition']) ? $_GET['condition'] : $this->config('defaults.condition')  ?: 0;
}


// Set default location
if ($location = $this->config('defaults.location')) {
    if (!isset($_GET['adr'])) {
        $_GET['adr'] = $location;
    }
}

// Set IDX Feed
if (!empty($searchCriteria['feed'])) {
    Util_IDX::switchFeed($searchCriteria['feed']);
}

$resultsLimit = $this->config('results.limit') ?: 3;

// AJAX Request
if (!empty($search)) {
    // Visitor's location
    $place = $this->config('place');

    // Visitor's location (as typed in)
    $input = $this->config('input');

    // Search by Radiuses
    $radiuses = $this->config('radiuses');

    // Search by Polygons
    $polygons = $this->config('polygons');

    // Remember seller's home details
    if (!empty($place)) {
        // Remember details in session
        $user->saveInfo('seller', array(
            'location'  => $place,
            'criteria'  => array(
                'type'      => $searchCriteria['type'],
                'subtype'   => $searchCriteria['subtype'],
                'bedrooms'  => $searchCriteria['beds'],
                'bathrooms' => $searchCriteria['baths'],
                'sqft'      => $searchCriteria['sqft'],
                'condition' => $searchCriteria['condition']
            )
        ));

        // If not default location, track it!
        if (!empty($input) && ($input !== $this->config('defaults.location'))) {
            try {
                $db = DB::get();
                $insert = $db->prepare("INSERT INTO `cma_location` SET `input` = :input, `place` = :place, `created` = NOW() ON DUPLICATE KEY UPDATE `count` = `count` + 1;");
                $insert->execute(array('input' => $input, 'place' => $place));
            // Ignore db error to keep the tool working
            } catch (PDOException $e) {
                //echo $e->getMessage();
            }
        }
    }

    // IDX & Database
    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();
    $sql_select = false;
    $sql_order = false;
    $sql_where = array();

    // Search in Shapes
    $sql_shapes = array();
    $sql_having = array();
    $col_lat = '`t1`.`' . $idx->field('Latitude') . '`';
    $col_lng = '`t1`.`' . $idx->field('Longitude') . '`';
    if (!empty($polygons)) {
        $polygons = $idx->buildWherePolygons($polygons, $sql_shapes, $sql_having, 't2.Point');
    }
    if (!empty($radiuses)) {
        $radiuses = $idx->buildWhereRadius($radiuses, $sql_shapes, $col_lat, $col_lng);
    }
    if (!empty($sql_shapes)) {
        $sql_where[] = '(' . implode(' OR ', $sql_shapes) . ')';
    }
    if (!empty($polygons)) {
        $sql_where[] = '`t1`.`' . $idx->field('ListingMLS') . '` IS NOT NULL';
    }
    if (!empty($radiuses)) {
        list($lat, $lng, $miles) = explode(',', $radiuses[0]);
        $lat = floatval($lat);
        $lng = floatval($lng);
        $miles = floatval($miles);
        $sql_order = "ORDER BY (((Acos("
            . "Sin((" . $lat . " * Pi() / 180)) * "
            . "Sin((" . $col_lat . " * Pi() / 180)) + "
            . "Cos((" . $lat . " * Pi() / 180)) * "
            . "Cos((" . $col_lat . " * Pi() / 180)) * "
            . "Cos(((" . $lng . " - " . $col_lng . ") * Pi() / 180))"
        . ")) * 180 / Pi()) * 60 * 1.1515) ASC";
    }

    // Set Search Criteria
    $searchRequest = array();
    if (!empty($searchCriteria['type'])) {
        $searchRequest['search_type'] = $searchCriteria['type'];
    }
    if (!empty($searchCriteria['subtype'])) {
        $searchRequest['search_subtype'] = $searchCriteria['subtype'];
    }

    // # of Bedrooms
    if (!empty($searchCriteria['beds'])) {
        if (preg_match('/\+$/', $searchCriteria['beds'])) {
            $searchRequest['minimum_bedrooms'] = intval($searchCriteria['beds']) - 1;
        } else {
            $searchRequest['minimum_bedrooms'] = ($searchCriteria['beds'] - 1);
            $searchRequest['maximum_bedrooms'] = ($searchCriteria['beds'] + 1);
        }
    }

    // # of Bathrooms
    if (!empty($searchCriteria['baths'])) {
        if (preg_match('/\+$/', $searchCriteria['baths'])) {
            $searchRequest['minimum_bathrooms'] = intval($searchCriteria['baths']) - 1;
        } else {
            $searchRequest['minimum_bathrooms'] = ($searchCriteria['baths'] - 1);
            $searchRequest['maximum_bathrooms'] = ($searchCriteria['baths'] + 1);
        }
    }

    // Sq.ft range
    if (!empty($searchCriteria['sqft'])) {
        list ($minSqft, $maxSqft) = explode('-', $searchCriteria['sqft']);
        $searchRequest['minimum_sqft'] = $minSqft;
        $searchRequest['maximum_sqft'] = $maxSqft;
    }

    // Set Request Data
    $oldRequest = $_REQUEST;
    $_REQUEST = $searchRequest;

    // Build SQL Query
    $search_vars = $idx->buildWhere($idx, $db_idx, 't1');
    $_REQUEST = $oldRequest;
    if (!empty($search_vars['search_where'])) {
        $sql_where[] = $search_vars['search_where'];
    }

    // Load dismissed listings
    $dismissed = $user->getDismissedListings($idx);

    // Exclude dismissed listings
    if (!empty($dismissed)) {
        $sql_where[] = "`t1`.`ListingMLS` NOT IN ('" . implode("', '", $dismissed) . "')";
    }

    // Search Statistics (# of listings, avg/min/max price)
    $col_mls = $idx->field('ListingMLS');
    $col_type = $idx->field('ListingType');
    $col_price = $idx->field('ListingPrice');
    $stats = $db_idx->fetchQuery("SELECT SQL_CACHE "
        . "COUNT(*) AS `total`"
        . ", AVG(`price`) AS `avg_price`"
        . ", MIN(`price`) AS `min_price`"
        . ", MAX(`price`) AS `max_price`"
    . " FROM (SELECT "
        . "`t1`.`" . $col_mls . "` AS `mls`"
        . ", " . $col_lat . ", " . $col_lng
        . ", `t1`.`" . $col_price . "` AS `price`"
        . (!empty($polygons) ? ', `t2`.`Point`' : '')
        . " FROM `" . $idx->getTable() . "` `t1`"
        . (!empty($polygons) ? " JOIN `" . $idx->getTable('geo') . "` `t2`"
            . " ON `t1`.`" . $col_mls . "` = `t2`.`" . $col_mls . "`"
            . " AND `t1`.`" . $col_type . "` = `t2`.`" . $col_type . "`"
        : "")
        . (!empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '')
        . (!empty($sql_having) ? ' HAVING ' . implode(' OR ', $sql_having) : '')
    . ") AS `stats`;");

    // Found results!
    $results = false;
    if (!empty($stats['total']) && $this->config('results')) {
        // Load saved favorites
        $bookmarked = $user->getSavedListings($idx);

        // Property results
        $query = $db_idx->query("SELECT SQL_CACHE "
            . $idx->selectColumns('`t1`.')
            . " FROM `" . $idx->getTable() . "` `t1`"
            . " JOIN (SELECT `t1`.`id`"
            . (!empty($polygons) ? ', `t2`.`Point`' : '')
            . " FROM `" . $idx->getTable() . "` `t1`"
            . (!empty($polygons) ? " JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`.`" . $col_mls . "` = `t2`.`" . $col_mls . "`"
                . " AND `t1`.`" . $col_type . "` = `t2`.`" . $col_type . "`"
            : "")
            . (!empty($sql_where) ? ' WHERE ' . implode(' AND ', $sql_where) : '')
            . (!empty($sql_having) ? ' HAVING ' . implode(' OR ', $sql_having) : '')
            . (!empty($sql_order) ? $sql_order : ' ORDER BY `t1`.`' . $col_price . '` DESC')
            . " LIMIT " . ((int) $resultsLimit)
            . ") p USING(`id`)"
            . (!empty($sql_order) ? $sql_order : ' ORDER BY `t1`.`' . $col_price . '` DESC')
        . ";");

        // HTML Results
        ob_start();
        $result_tpl = Page::locateTemplate('idx', 'misc', 'result');
        while ($result = $db_idx->fetchArray($query)) {
            $result = Util_IDX::parseListing($idx, $db_idx, $result);
            $result['ListingImage'] = Format::thumbUrl($result['ListingImage'], '600x400');
            include $result_tpl;
        }
        $results = ob_get_clean();
    }

    // Centerpoint
    unset($latitude, $longitude, $miles);
    if (!empty($radiuses)) {
        list ($latitude, $longitude, $miles) = explode(',', $radiuses[0]);
    }

    // JSON Response
    header('Content-Type: application/json');
    die(json_encode(array(
        'total'     => intval($stats['total']),
        'min_price' => intval($stats['min_price']),
        'avg_price' => intval($stats['avg_price']),
        'max_price' => intval($stats['max_price']),
        'results'   => (!empty($results) ? $results : ($this->config('results') ? '<p>No comparable properties were found &ndash; <a class="cta-link">Receive a free in-person assessment</a>.</p>' : null)),
        'criteria'  => $searchRequest,
        'url'       => Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'] . '?' . http_build_query(array_merge($searchRequest, array('refine' => true, 'map' => array_filter(array(
            'radius'    => json_encode($radiuses),
            'polygon'   => json_encode($polygons),
            'latitude'  => $latitude,
            'longitude' => $longitude
        )))))
    )));
} else {
    // Load property types
    $idx_types = IDX_Panel::get('Type', array('placeholder' => false))->getOptions();

    // Load property sub-types
    $idx_subtypes = IDX_Panel::get('Subtype', array('placeholder' => false))->getOptions();
}
