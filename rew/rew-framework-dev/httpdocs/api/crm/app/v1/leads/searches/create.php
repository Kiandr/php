<?php

// Include IDX common files
require $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

// Fetch requested lead
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
    $errors[] = 'The specified lead could not be found';
    return;
}

// Required parameters
$required = array('title', 'criteria', 'feed', 'source');

// Check POST
foreach ($required as $field) {
    if (!isset($_POST[$field])) {
        $errors[] = 'Required parameter is missing: \'' . $field . '\'';
    }
}

// Require valid criteria format
if (!empty($_POST['criteria']) && !is_array($_POST['criteria'])) {
    $errors[] = 'Criteria was not specified in the expected format';
}

// Require no errors
if (!empty($errors)) {
    return;
}

// Validate feed
try {
    $idx = Util_IDX::getIdx($_POST['feed']);
} catch (Exception $ex) {
    $errors[] = 'An error occurred while retrieving the specified feed: ' . $ex->getMessage();
    return;
}

// Serialize criteria
$criteria = serialize($_POST['criteria']);

// Check existing record
$where = array(
    '$eq' => array(
        'user_id' => $lead['id'],
        'title' => $_POST['title'],
        'criteria' => $criteria,
        'table' => $_POST['source'],
        'idx' => $_POST['feed'],
    )
);

if ($existing = $db->{'users_searches'}->search($where)->fetch()) {
    $app->response->status(409);
    $errors[] = 'A saved search with the same criteria already exists for this user';
    return;
}

// Record data
$data = array(
    'user_id'           => $lead['id'],
    'title'             => $_POST['title'],
    'criteria'          => $criteria,
    'table'             => $_POST['source'],
    'idx'               => $_POST['feed'],
    "`timestamp_created` = NOW()",
);

// Optional data
if (!empty($_POST['frequency']) && in_array($_POST['frequency'], array('never', 'daily', 'weekly', 'monthly'))) {
    $data['frequency'] = $_POST['frequency'];
}

// Private fields - undocumented to the public
if ($_POST['_suppress_alerts'] === '1') {
    // Set API Application ID
    $request = $app->request();
    $headers = $request->headers();
    if ($application = $db->{'api_applications'}->search(array('$eq' => array('api_key' => $headers['X_REW_API_KEY'])))->fetch()) {
        $data['source_app_id'] = $application['id'];
    }
}

// Insert record
try {
    $row = $db->{'users_searches'}->insert($data);

    // Increment saved count
    $db->query("UPDATE `users` SET `num_saved` = `num_saved` + 1 WHERE `id` = '" . $lead['id'] . "';");

    // Update score
    Backend_Lead::load($lead['id'])->updateScore();

    // API object
    $object = new API_Object_Lead_Search($db, $row);

    // Data subset
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'The saved search could not be created: ' . $ex->getMessage();
}
