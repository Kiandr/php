<?php

// Fetch requested group
$db = DB::get('users');

// Search where
$where = array(
    '$eq' => array(
        'id' => $id,
    ),
);

// Fetch group row
if (!($row = $db->{'groups'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified Group could not be found';
    return;
}

// API Object
$object = new API_Object_Group($db, $row);
$json = $object->getData();
