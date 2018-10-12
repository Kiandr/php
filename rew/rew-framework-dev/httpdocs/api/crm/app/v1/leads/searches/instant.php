<?php

/**
 * Returns An Array Of Pre-built Users Searches Search Components
 *
 * Example Response
 * HTTP/1.1 200 OK
 * Content-Type: application/json
 *
 * {
 *     "data" : [
 *         {
 *             "id" : 168,
 *             "search_query" => {
 *                 "select"   => "SELECT `t1`.`id` AS 'id', `t1`.`ListingMLS` AS 'ListingMLS', `t1`.`ListingPrice` AS 'ListingPrice', `t1`.`ListingStatus` AS 'ListingStatus', `t1`.`ListingRemarks` AS 'ListingRemarks', `t1`.`ListingImage` AS 'ListingImage', `t1`.`AddressCity` AS 'AddressCity', `t1`.`NumberOfBedrooms` AS 'NumberOfBedrooms', `t1`.`NumberOfBathrooms` AS 'NumberOfBathrooms', `t1`.`ListingOffice` AS 'ListingOffice', `t1`.`ListingAgent` AS 'ListingAgent'",
 *                 "where"    => "WHERE `t1`.`ListingPrice` <= '200000' AND `t1`.`ListingDOM` <= '7'",
 *                 "order"    => "ORDER BY `t1`.`timestamp_created` DESC",
 *                 "limit"    => "LIMIT 10",
 *                 "polygons" => true
 *             }
 *         }
 *     ]
 * }
 */

// Include IDX Configuration
require_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';


// Set To Requested Feed
Settings::getInstance()->IDX_FEED = $feed;


// Fetch Instant Searches
$db = DB::get('users');


// IDX Object
$idx = Util_IDX::getIdx($feed);


// Candidate Instant Searches Query
$query = "
SELECT `t1`.`id`, `t1`.`criteria`
FROM `users_searches` `t1`
    LEFT JOIN `users` `t2`
    ON `t1`.`user_id` = `t2`.`id`
    AND `t2`.`id` IS NOT NULL
WHERE `t1`.`id` > :id
AND `t1`.`frequency` = 'immediately'
AND `t2`.`opt_searches` = 'in'
AND `t1`.`idx` = :feed
AND `t2`.`bounced` != 'true'
AND `t2`.`fbl` != 'true'
AND `t1`.`source_app_id` IS NULL " .
// Only Include Map-Based Searches If Instant Searches Service Requests Map Searches
// And Only Include Non-Map Searches If Instant Searches Service Does Not Request Map Searches
"AND ( " .
(!empty($_GET['geo']) ?
    "`criteria` NOT LIKE '%s:7:\"polygon\";s:0:\"\";%'
    OR
    `criteria` NOT LIKE '%s:6:\"radius\";s:0:\"\";%'
    OR
    `criteria` NOT LIKE '%s:6:\"bounds\";s:1:\"0\";%'"
:
    "`criteria` LIKE '%s:7:\"polygon\";s:0:\"\";%'
    AND
    `criteria` LIKE '%s:6:\"radius\";s:0:\"\";%'
    AND
    `criteria` LIKE '%s:6:\"bounds\";s:1:\"0\";%'"
) .
")
ORDER BY `t1`.`id` ASC
LIMIT :limit";

$stmt = $db->prepare($query);
$stmt->bindValue(':feed', $feed, PDO::PARAM_STR);
$stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int) (!empty($limit) && is_numeric($limit) ? $limit : 5000), PDO::PARAM_INT);
// Default Limit Of 5000 Ensures That:
// - The Execution Time Is Within Timeout Limits Of Web Server
// - The Amount Of Data Transferred Over The Network At One Time Is Relatively Small
// - The Memory Usage For The Instant Search Worker Doesn't Exceed Its Limit
// - Further CRM API Requests Do Not Time Out Due To Large Work Load

$stmt->execute();

$searches = array();
while ($search = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // $_REQUEST Array
    $_REQUEST = array();

    // Search Criteria
    if (!empty($search['criteria'])) {
        // Searchable Fields
        $search_fields = search_fields($idx);
        $search_fields = array_keys($search_fields);

        // Search Criteria
        $criteria = unserialize($search['criteria']);
        if (!empty($criteria) && is_array($criteria)) {
            // Set $_REQUEST
            foreach ($criteria as $key => $val) {
                if (in_array($key, array_merge(array('map', 'view', 'search_location'), $search_fields))) {
                    if (!isset($_REQUEST[$key])) {
                        $_REQUEST[$key] = $val;
                    }
                }
            }
        }

        // Snippet, Over-Ride Feed Defaults
        $_REQUEST['search_city'] = isset($_REQUEST['search_city']) ? $_REQUEST['search_city'] : false;
        $_REQUEST['search_type'] = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : false;
    }

    //Latitude / Longitude Columns
    $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
    $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

    // Build Query
    $search_vars        = $idx->buildWhere($idx, $db_idx, 't1');
    $search_where       = $search_vars['search_where'];
    $search_title       = $search_vars['search_title'];
    $search_criteria    = $search_vars['search_criteria'];

    // Query Collection
    $search_where = !empty($search_where) ? array($search_where) : array();

    /**
     * Map Queries
     */

    // HAVING Queries
    $search_having = array();

    // Search Group
    $search_group = array();

    // Latitude / Longitude Columns
    $col_latitude  = "`t1`.`" . $idx->field('Latitude') . "`";
    $col_longitude = "`t1`.`" . $idx->field('Longitude') . "`";

    // Search in Bounds
    if (!empty($_REQUEST['map']['bounds']) && Settings::getInstance()->IDX_FEED != 'cms') {
        $idx->buildWhereBounds($_REQUEST['map']['ne'], $_REQUEST['map']['sw'], $search_group, $col_latitude, $col_longitude);
    }

    // Search in Radiuses
    $idx->buildWhereRadius($_REQUEST['map']['radius'], $search_group, $col_latitude, $col_longitude);

    // Search in Polygons
    $polygons = $idx->buildWherePolygons($_REQUEST['map']['polygon'], $search_group, $search_having, 't2.Point');
    if (!empty($polygons)) {
        $search_where[] = "`t1`.`" . $idx->field('ListingMLS') . "` IS NOT NULL";
    }

    // Add Mapping Search Groups to Search Criteria
    if (!empty($search_group)) {
        $search_where[] = '(' . implode(' OR ', $search_group) . ')';
    }

    // Query String (WHERE & HAVING)
    $search_where = (!empty($search_where) ? implode(' AND ', $search_where) : '') . (!empty($search_having) ? " HAVING " . implode(' OR ', $search_having) : '');

    $select_columns = array(
        $idx->field('id'),
        $idx->field('AddressCity'),
        $idx->field('AddressState'),
        $idx->field('ListingPrice'),
        $idx->field('ListingImage'),
        $idx->field('ListingMLS'),
        $idx->field('NumberOfBedrooms'),
        $idx->field('NumberOfBathrooms'),
        $idx->field('ListingStatus'),
        $idx->field('ListingRemarks'),
        $idx->field('ListingAgent'),
        $idx->field('ListingOffice'),
    );

    // New Listings Query Components
    $select = "SELECT " . $idx->selectColumns("`t1`.", $select_columns);
    $where  = "WHERE " . $search_where;
    $order  = "ORDER BY `t1`.`timestamp_created` DESC";
    $limit  = "LIMIT 10";


    // Add To Searches
    $json[] =  array(
        'id'           => $search['id'],
        'search_query' => array(
            'select'   => $select,
            'where'    => $where,
            'order'    => $order,
            'limit'    => $limit,
            'polygons' => !empty($polygons)
        )
    );
}
