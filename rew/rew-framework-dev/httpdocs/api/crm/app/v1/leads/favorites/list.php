<?php

// Fetch favorites
$db = DB::get('users');
$favorites = array();

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
if (!empty($_GET['mls_number'])) {
    $where['$eq']['mls_number'] = $_GET['mls_number'];
}
if (!empty($_GET['type'])) {
    $where['$eq']['type'] = $_GET['type'];
}

// Fetch collection
$rows = $db->getCollection('users_listings')->search($where)->fetchAll();

// Process results
foreach ($rows as $row) {
    // API object
    $object = new API_Object_Lead_Favorite($db, $row);

    // Add to collection
    $favorites[] = $object->getData();
}

// Set favorites
$json = $favorites;
