<?php

// Fetch favorite
$db = DB::get('users');

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
        'id' => $id,
    ),
);

// Fetch favorite row
if (!($row = $db->{'users_listings'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified ' . Locale::spell('Favorite') . ' could not be found';
    return;
}

// API Object
$object = new API_Object_Lead_Favorite($db, $row);
$json = $object->getData();
