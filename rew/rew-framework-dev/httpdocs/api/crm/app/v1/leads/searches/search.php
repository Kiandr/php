<?php

// Fetch saved search
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

// Fetch saved search row
if (!($row = $db->{'users_searches'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified Saved Search could not be found';
    return;
}

// API Object
$object = new API_Object_Lead_Search($db, $row);
$json = $object->getData();
