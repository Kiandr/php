<?php

// Required parameters
$required = array('name', 'data');

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

// Valid hooks
$valid_hooks = array(
    Hooks::HOOK_LEAD_TEXT_INCOMING => array(
        'require' => array('to', 'from'),
        'handler' => function (&$errors, $data) {
            $media = $data['media'];
            $message = $data['message'];
            if (empty($media) && empty($message)) {
                $errors[] = 'Required \'data\' key is missing: ' . json_encode('message', true);
                return false;
            }
            return array($data['to'], $data['from'], $message, $media, $data['request']);
        }
    )
);

// Hook details
$hook = array(
    'name' => $_POST['name'],
    'data' => $_POST['data']
);

// Validate type
if (!in_array($hook['name'], array_keys($valid_hooks))) {
    $errors[] = 'The specified hook is invalid: ' . json_encode($hook['name'], true);
    return;
}

// Check details keys
$valid_hook = $valid_hooks[$hook['name']];
foreach ($valid_hook['require'] as $require) {
    if (empty($hook['data'][$require])) {
        $errors[] = 'Required \'data\' key is missing: ' . json_encode($require, true);
    }
}

// Require no errors
if (!empty($errors)) {
    return;
}

try {
    // Handler
    $handler = $valid_hook['handler'];
    if (!is_callable($handler)) {
        $errors[] = 'Invalid handler for hook: ' . json_encode($hook['name'], true);
    } else {
        $params = $handler($errors, $hook['data']);
        if (!empty($errors)) {
            return;
        }
        $return = call_user_func_array(array(Hooks::hook($hook['name']), 'run'), $params);
    }

    // Empty response
    $json = null;
} catch (Exception $ex) {
    $errors[] = 'Failed to trigger hook: ' . $ex->getMessage();
}
