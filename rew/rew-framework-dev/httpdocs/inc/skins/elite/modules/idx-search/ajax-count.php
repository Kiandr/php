<?php

// Load default "module.php"
$controller = $this->locateFile('module.php', __DIR__ . '/module.php');
if (!empty($controller)) {
    require $controller;
}

$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Default 5 minutes
$cache_time = $this->config('cache_time') ?: 300;

$searches = array(
    'count' => array(
        'count' => "*",
        'label' => trim((Settings::getInstance()->IDX_FEEDS ? Format::htmlspecialchars(Settings::getInstance()->IDX_FEEDS[Settings::getInstance()->IDX_FEED]['title']) . ' ': '') . ' Listings'),
        'value' => ''
    )
);

$mapping_required_fields = array();

$max_age = $idx->getMaxAgeOfNewListingInDays();
$search_fields = search_fields($idx);
foreach ($search_fields as $search_field) {
    $form_field = $search_field['form_field'];

    switch ($form_field) {
        case 'maximum_dom':
        case 'maximum_dow':
        case 'search_new':
            $panel = IDX_Panel::get('Age');
            if (!$panel->isAvailable()) {
                continue;
            }
            $value = stripos($search_field['idx_field'], 'timestamp') !== false ? '-' . ((int) $max_age) . ' DAY' : ((int) $max_age);
            $searches[$form_field] = array(
                'count' => "IF(" . $db_idx->buildQueryString($idx->field($search_field['idx_field']), $value, $search_field['match'], null, $idx) . ", 1, NULL)",
                'label' => $form_field == 'search_new' ? $search_field['name'] : 'New Listings',
                'value' => $value
            );
            $mapping_required_fields[] = $idx->field($search_field['idx_field']);
            break;
        case 'search_reduced_price':
            $panel = IDX_Panel::get('ReducedPrice');
            if (!$panel->isAvailable()) {
                continue;
            }
            $value = '-' . ((int) $max_age) . ' DAY';
            $searches[$form_field] = array(
                'count' => "IF(" . $db_idx->buildQueryString(array_map(function ($field) use ($idx) {
                    return $idx->field($field);
                }, $search_field['idx_fields']), $value, $search_field['match'], null, $idx) . ", 1, NULL)",
                'label' => $search_field['name'],
                'value' => $value,
            );
            $mapping_required_fields = array_merge(array_values($search_field['idx_fields']), $mapping_required_fields);
            break;
        case 'search_has_openhouse':
            $panel = IDX_Panel::get('HasOpenHouse');
            if (!$panel->isAvailable()) {
                continue;
            }
            $value = 'Y';
            $searches[$form_field] = array(
                'count' => "IF(" . $db_idx->buildQueryString($idx->field($search_field['idx_field']), $value, $search_field['match'], null, $idx) . ", 1, NULL)",
                'label' => $search_field['name'],
                'value' => $value
            );
            $mapping_required_fields[] = $idx->field($search_field['idx_field']);
            break;
    }
}

if (isset($searches['search_new'])) {
    // If search_new is set for this feed, ignore dom/dow.
    unset($searches['maximum_dow'], $searches['maximum_dom']);
}

$untaggable_fields = array();
// Build Query, excluding our sectional search criteria. This way if a visitor opens a reduced listings
// search, for example, and then they go to refine, they will still have their original criteria.
// At the same time, figure out any fields we don't want to tag - for example dom if the dom panel isn't displayed.
$search_vars = $idx->buildWhere($idx, $db_idx, 't1', array_filter($search_fields, function ($search_field) use ($searches, $panels, &$untaggable_fields) {
    if ($search_field['form_field'] != 'count' && !empty($searches[$search_field['form_field']])) {
        // Before we actually strip this, lets look at our search panels.
        foreach ($panels as $panel) {
            $skip = array();
            foreach ($panel->getInputs() as $input) {
                if ($input == $search_field['form_field'] && !$panel->isHidden()) {
                    return true;
                } else {
                    $skip[] = $search_field['form_field'];
                }
            }

            $untaggable_fields += $skip;
        }

        return false;
    }

    return true;
}));

$untaggable_fields = array_unique($untaggable_fields);

$search_criteria = $search_vars['search_criteria'];
$search_where = $search_vars['search_where'];
$search_title = $search_vars['search_title'];

// WHERE Queries
$search_where = !empty($search_where) ? array($search_where) : array();

// HAVING Queries
$search_having = array();

// Search Group
$search_group = array();

// Latitude / Longitude Columns
$col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
$col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

if (!empty($_REQUEST['map']['latitude']) && !empty($_REQUEST['map']['longitude'])) {
    $search_criteria['map']['latitude'] = $_REQUEST['map']['latitude'];
    $search_criteria['map']['longitude'] = $_REQUEST['map']['longitude'];
    $search_criteria['map']['zoom'] = $_REQUEST['map']['zoom'];
}

// Search in Bounds
if (!empty($_REQUEST['map']['bounds']) && Settings::getInstance()->IDX_FEED != 'cms') {
    $bounds = $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'], $search_group, $col_latitude, $col_longitude);
    if (!empty($bounds)) {
        $search_criteria['map']['bounds'] = 1;
    }
}

// Search in Radiuses
$radiuses = $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);
if (!empty($radiuses)) {
    $search_criteria['map']['radius'] = $_REQUEST['map']['radius'];
}

// Search in Polygons
$polygons = $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
if (!empty($polygons)) {
    // inc/classes/IDX/Panel/Polygon.php the tag is hardcoded to value of 1
    $search_criteria['map']['polygon'] = $_REQUEST['map']['polygon'];
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

// Query String (WHERE & HAVING)
$search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

$search_query = 'SELECT ';

// Perform searches
foreach ($searches as $key => $search) {
    $search_query .= "COUNT(" . $search['count'] . ") AS `" . $key . "`, ";
}

$search_query = rtrim($search_query, ", ");

// Count Search Query
if (empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING']) || empty($polygons)) {
    $search_query .= " FROM `" . $idx->getTable() . "` `t1`" . (!empty($search_where) ? ' WHERE ' . $search_where : '');
} else {
    if ($idx->getLink() == 'cms') {
        $search_query .= " FROM `" . $idx->getTable() . "` `t1`" . (!empty($search_where) ? ' WHERE ' . $search_where : '');
    } else {
        $search_query .= " FROM "
                . "(SELECT `t1`.`" . $idx->field('ListingMLS') . "` AS `total`, `t2`.`Point`"
                . (!empty($mapping_required_fields) ? ", `t1`." . implode(', `t1`.', $mapping_required_fields) : "" )
                . " FROM `" . $idx->getTable() . "` `t1` JOIN `" . $idx->getTable('geo') . "` `t2`"
                . " ON `t1`.`" . $idx->field('ListingMLS') . "` = `t2`.`ListingMLS`"
                . " AND `t1`.`" . $idx->field('ListingType') . "` = `t2`.`ListingType`"
                . (!empty($search_where) ? ' WHERE ' . $search_where : '')
            . ") as listings";
    }
}
$cache_key = __FILE__ . $idx->getName() . '.' . $idx->getTable() . '.' . $search_query;
$results = Cache::getCache($cache_key);
if (!$results) {
    $results = $db_idx->fetchQuery($search_query);

    Cache::setCache($cache_key, $results, false, $cache_time);
}
