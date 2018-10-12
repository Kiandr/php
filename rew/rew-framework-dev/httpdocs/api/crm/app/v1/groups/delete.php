<?php

// Fetch requested group
$db = DB::get('users');

// Limit IDs
if ($id < 3) {
    $errors[] = 'The specified Group cannot be deleted via the API';
    return;
}

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

// Delete
try {
    $db->{'groups'}->delete($where);
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'The specified Group could not be deleted';
    return;
}
