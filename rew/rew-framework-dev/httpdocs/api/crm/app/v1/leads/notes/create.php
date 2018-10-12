<?php

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
$required = array('note');

// Check POST
foreach ($required as $field) {
    if (!isset($_POST[$field])) {
        $errors[] = 'Required parameter is missing: \'' . $field . '\'';
    }
}

// Require no errors
if (!empty($errors)) {
    return;
}

try {
    $query = $db->prepare("INSERT INTO `users_notes` SET `user_id` = :user_id, `note` = :note, `share` = 'true', `timestamp` = NOW();");
    $query->execute(array('user_id' => $lead['id'], 'note' => $_POST['note']));
} catch (Exception $ex) {
    $errors[] = 'Failed to create lead note: ' . $ex->getMessage();
}
