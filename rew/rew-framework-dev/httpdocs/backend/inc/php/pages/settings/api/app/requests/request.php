<?php

// Get Authorization Managers
$settingsAuth = new REW\Backend\Auth\SettingsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$settingsAuth->canManageApi($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage the API')
    );
}

// Application ID
$_GET['app_id'] = !empty($_POST['app_id']) ? intval($_POST['app_id']) : intval($_GET['app_id']);

// Get Selected Row
$result  = mysql_query("SELECT * FROM `api_applications` WHERE `id` = '" . mysql_real_escape_string($_GET['app_id']) . "';");
$application = mysql_fetch_array($result);

// Require application
if (empty($application)) {
    return;
}

// Request ID
$_GET['id'] = !empty($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

// Get Selected Row
$result  = mysql_query("SELECT *, UNIX_TIMESTAMP(`timestamp`) AS `timestamp_created` FROM `api_requests` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "' AND `app_id` = '" . $application['id'] . "';");
$request = mysql_fetch_array($result);

// Require request
if (empty($request)) {
    return;
}

// Censor internal IP
if (strpos($request['ip'], '192.') === 0 || strpos($request['ip'], '10.') === 0) {
    $request['ip'] = 'REW Internal';
}

// Format duration
if ($request['duration'] >= 1) {
    $request['duration'] = number_format($request['duration'], 4) . ' sec';
} else {
    $request['duration'] = number_format($request['duration'] * 1000, 2) . ' ms';
}

// Format response
if (!empty($request['response'])) {
    $json_response = json_decode($request['response'], true);
    if (!empty($json_response)) {
        $request['response'] = print_r($json_response, true);
    }
}

// Headers
$headers = array();
if (!empty($request['headers'])) {
    $headers = json_decode($request['headers'], true);
}

// GET
$get = array();
if (!empty($request['get'])) {
    $get = json_decode($request['get'], true);
}

// POST
$post = array();
if (!empty($request['post'])) {
    $post = json_decode($request['post'], true);
}

// Hide private fields
$private_fields = array('_suppress_alerts');
if (!empty($get)) {
    foreach ($get as $k => $v) {
        if (in_array($k, $private_fields)) {
            unset($get[$k]);
        }
    }
}
if (!empty($post)) {
    foreach ($post as $k => $v) {
        if (in_array($k, $private_fields)) {
            unset($post[$k]);
        }
    }
}
