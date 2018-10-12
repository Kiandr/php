<?php

// Fetch saved searches
$db = DB::get('users');
$searches = array();

// Search where
$where = array(
    '$eq' => array(
        'email' => $email,
    ),
);

// Fetch lead row
if (!($lead = $db->{'users'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified Lead could not be found';
    return;
}

// Search where
$where = array(
    '$eq' => array(
        'user_id' => $lead['id'],
    ),
);

// Search filters
if (!empty($_GET['feed'])) {
    $where['$eq']['idx'] = $_GET['feed'];
}
if (!empty($_GET['title'])) {
    $where['$eq']['title'] = $_GET['title'];
}
if (!empty($_GET['criteria']) && is_array($_GET['criteria'])) {
    $criteria = serialize($_GET['criteria']);
    $where['$eq']['criteria'] = $criteria;
}

// Fetch collection
$rows = $db->getCollection('users_searches')->search($where)->fetchAll();

// Process results
foreach ($rows as $row) {
    // API object
    $object = new API_Object_Lead_Search($db, $row);

    // Add to collection
    $searches[] = $object->getData();
}

// Set saved searches
$json = $searches;
