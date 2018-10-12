<?php

// Update saved search
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
if (!($search = $db->{'users_searches'}->search($where)->fetch())) {
    $app->response->status(404);
    $errors[] = 'The specified Saved Search could not be found';
    return;
}

// Require valid criteria format
if (!empty($_POST['criteria']) && !is_array($_POST['criteria'])) {
    $errors[] = 'Criteria was not specified in the expected format';
}

// Require no errors
if (!empty($errors)) {
    return;
}

// SET data
$data = array();

// Optional data
if (isset($_POST['title'])) {
    $data['title'] = $_POST['title'];
}
if (isset($_POST['frequency']) && in_array($_POST['frequency'], array('never', 'daily', 'weekly', 'monthly'))) {
    $data['frequency'] = $_POST['frequency'];
}
if (isset($_POST['criteria'])) {
    $criteria = serialize($_POST['criteria']);
    $data['criteria'] = $criteria;
}

// Require data
if (empty($data)) {
    $object = new API_Object_Lead_Search($db, $search);
    $json = $object->getData();
    return;
}

// Update saved search
try {
    $db->{'users_searches'}->update($data, $where);

    // Updated search
    $search = $db->{'users_searches'}->search($where)->fetch();

    // Return saved search object
    $object = new API_Object_Lead_Search($db, $search);
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'The Saved Search not be updated: ' . $ex->getMessage();
}
