<?php

// Required parameters
$required = array('name');

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

// SET data
$data = array(
    'name' => $_POST['name'],
);

// Group properties - required
$data['agent_id'] = !empty($_POST['agent_id']) ? intval($_POST['agent_id']) : null;
$data['user'] = !empty($_POST['system']) && in_array($_POST['system'], array(true, 1)) ? 'false' : 'true';

// Description - optional
if (!empty($_POST['description'])) {
    $data['description'] = $_POST['description'];
}

// Check duplicate
try {
    $db = DB::get('users');
    $existing = $db->{'groups'}->search(array('$eq' => $data))->fetchAll();
    if (!empty($existing)) {
        // Existing groups
        $groups = array();
        foreach ($existing as $group) {
            $object = new API_Object_Group($db, $group);
            $groups[] = $object->getData();
        }

        $error_details = $groups;
        $app->response->status(409);
        $errors[] = 'A group with the same properties already exists';
        return;
    }
} catch (Exception $ex) {
    $errors[] = 'Failed to check existing groups: ' . $ex->getMessage();
    return;
}

// Insert record
try {
    $row = $db->{'groups'}->insert($data);

    // API object
    $object = new API_Object_Group($db, $row);

    // Data subset
    $json = $object->getData();
} catch (Exception $ex) {
    $errors[] = 'The group could not be created: ' . $ex->getMessage();
}
