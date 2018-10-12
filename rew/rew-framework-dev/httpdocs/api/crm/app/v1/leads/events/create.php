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
$required = array('type', 'subtype', 'details');

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

// Valid types and subtypes
$valid_events = array(
    'Action' => array(
        'subtypes' => array('FormSubmission'),
        'fields' => array('page', 'form', 'data'),
    ),
    'Phone' => array(
        'subtypes' => array('Attempt', 'Contact', 'Invalid', 'Voicemail'),
        'fields' => array('details'),
    ),
);

// Validate type
if (!in_array($_POST['type'], array_keys($valid_events))) {
    $errors[] = 'The specified event type is invalid: \'' . $_POST['type'] . '\'';
    return;
}

// Validate subtype
if (!in_array($_POST['subtype'], $valid_events[$_POST['type']]['subtypes'])) {
    $errors[] = 'The specified event sub-type is invalid: \'' . $_POST['subtype'] . '\'';
    return;
}

// Validate details
if (!is_array($_POST['details'])) {
    $errors[] = 'Required parameter must be an array: \'details\'';
    return;
}

// Check details keys
foreach ($valid_events[$_POST['type']]['fields'] as $field) {
    if (empty($_POST['details'][$field])) {
        $errors[] = 'Required \'details\' key is missing: \'' . $field . '\'';
    }
}

// Require no errors
if (!empty($errors)) {
    return;
}

try {
    // Create event
    $event_class = 'History_Event_' . $_POST['type'] . '_' . $_POST['subtype'];
    $event = new $event_class($_POST['details'], array(new History_User_Lead($lead['id'])));

    // Save
    $event->save();

    // Update score
    Backend_Lead::load($lead['id'])->updateScore();

    // API object
    $object = new API_Object_Event($db, $event);
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'Failed to create history event: ' . $ex->getMessage();
}
