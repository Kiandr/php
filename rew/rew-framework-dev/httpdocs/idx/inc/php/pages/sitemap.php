<?php

// IDX Sitemap Not Enabled, Re-Direct
if (empty(Settings::getInstance()->MODULES['REW_IDX_SITEMAP'])) {
    header('Location: /');
    exit;
}

// Remember this Page
$user->setBackUrl($_SERVER['REQUEST_URI']);

// Search Cities
$sql_cities = array();

// Search Client City List $_CLIENT['city_list']
global $_CLIENT;
if (!empty($_CLIENT['city_list'])) {
    if (empty($_REQUEST['search_city'])) {
        foreach ($_CLIENT['city_list'] as $city) {
            $sql_cities[] = "`" . $idx->field('AddressCity') . "` = '" . $db_idx->cleanInput($city['value']) . "'";
        }
    }
}

// SQL WHERE
$sql_where = "`" . $idx->field('AddressCity') . "` IS NOT NULL AND  `" . $idx->field('AddressCity') . "` != ''" . (!empty($sql_cities) ? " AND (" . implode(" OR ", $sql_cities) . ")" : "");

// Any global criteria
$idx->executeSearchWhereCallback($sql_where);

// Current Page
$current_page = !empty($_GET['p']) ? $_GET['p'] : 1;

// Page Limit
$page_limit = 75;

// Count Search Query
$search_count_query = "SELECT SQL_CACHE COUNT(*) AS `total` FROM `" . $idx->getTable() . "` `t1` WHERE " . $sql_where;

// Execute Count Query
$search_count = $db_idx->fetchQuery($search_count_query);

// Pagination
$pagination = generate_page_bar($search_count['total'], $_GET['p'], $page_limit);

// Incorrect Current Page, Respond with 404
if ($current_page > $pagination['pages']) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
    $current_page = $pagination['pages'];
    $pagination = generate_page_bar($search_count['total'], $current_page, $page_limit);
} elseif ($current_page < 0) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
    $current_page = 1;
    $pagination = generate_page_bar($search_count['total'], $current_page, $page_limit);
}

// Set Prev/Next
if (!empty($pagination['prev'])) {
    $page->info('link.prev', $pagination['prev']['url']);
}
if (!empty($pagination['next'])) {
    $page->info('link.next', $pagination['next']['url']);
}

// Build LIMIT
if ($search_count['total'] > $page_limit) {
    $limitvalue = ($current_page * $page_limit) - $page_limit;
    $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
    $sql_limit = " LIMIT " . $limitvalue . "," . $page_limit;
} else {
    $sql_limit = '';
}

// Build ORDER BY
$sql_order = "ORDER BY `" . $idx->field('AddressCity') . "` ASC";

// Create Search Query
$search_query = "SELECT SQL_CACHE " . $idx->selectColumns("`t1`.")
    . " FROM `" . $idx->getTable() . "` `t1`"
    . " JOIN (SELECT `t1`.`id` FROM `" . $idx->getTable() . "` `t1`"
    . " WHERE " . $sql_where
    . $sql_order . $sql_limit . ") p USING(`id`)" . $sql_order;

// Execute Search Query
$search_results = $db_idx->query($search_query);

// Sitemap Groups
$groups = array();

// Listing Results Found
if (!empty($search_count['total'])) {
    // Build Collection
    while ($search_result = $db_idx->fetchArray($search_results)) {
        // Parse Listing
        $result = Util_IDX::parseListing($idx, $db_idx, $search_result);

        // Format City Name
        $result['AddressCity'] = ucwords(strtolower(trim($result['AddressCity'])));

        // City Group
        $groups[$result['AddressCity']] = is_array($groups[$result['AddressCity']]) ? $groups[$result['AddressCity']] : array();

        // Group by City
        $groups[$result['AddressCity']][] = $result;
    }
}

// Dynamic Page Title
$page_title = Lang::write('MLS') . ' Property Listing Sitemap' . (!empty($current_page) && is_numeric($current_page) ? ' - Page #' . $current_page : '');

// Dynamic Meta Description
if (!empty($groups)) {
    $meta_desc = implode(', ', array_keys($groups));
}

// Get the get variables that we will need for pagination links
parse_str(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1), $query_string);
