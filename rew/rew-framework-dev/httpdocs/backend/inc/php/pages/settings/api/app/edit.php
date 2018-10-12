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
$_GET['id'] = !empty($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

// Get Selected Row
$result  = mysql_query("SELECT * FROM `api_applications` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "';");
$application = mysql_fetch_array($result);

// Require application
if (empty($application)) {
    return;
}

// Generate new key?
if (isset($_GET['generate'])) {
    // Random key
    $api_key = hash('sha256', uniqid('', true) . $_SERVER['HTTP_HOST']);

    // Build query
    $sql = "UPDATE `api_applications` SET `api_key` = '" . mysql_real_escape_string($api_key) . "' WHERE `id` = '" . mysql_real_escape_string($application['id']) . "';";
    if (mysql_query($sql)) {
        // Success
        $success[] = __('A new API Key has been successfully generated.');

        // Save Notices
        $authuser->setNotices($success, $errors);

        // Redirect
        header('Location: ?id=' . $application['id']);
        exit;
    } else {
        // Query error
        $errors[] = __('A new API Key could not be generated.');
    }
}

// Form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim input
    foreach ($_POST as $k => $v) {
        if (is_string($v)) {
            $_POST[$k] = trim($v);
        }
    }

    // Required Fields
    $required = array(
        array('value' => 'name',    'title' => __('Name')),
        array('value' => 'enabled', 'title' => __('Active')),
    );

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] =  __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Enum values
        $enabled = $_POST['enabled'] === 'Y' ? 'Y' : 'N';

        // Build query
        $sql = "UPDATE `api_applications` SET "
                    . "`name`		= '" . mysql_real_escape_string($_POST['name']) . "', "
                    . "`enabled`	= '" . mysql_real_escape_string($enabled) . "' "
                . "WHERE `id` = '" . mysql_real_escape_string($application['id']) . "';";

        // Execute
        if (mysql_query($sql)) {
            // Success
            $success[] = __('Your changes have successfully been saved.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect
            header('Location: ?id=' . $application['id']);
            exit;
        } else {
            // Query error
            $errors[] = __('The application could not be saved.');
        }
    }
}

// Set POST fields
$post_fields = array('name', 'enabled');
foreach ($post_fields as $field) {
    if (!isset($_POST[$field])) {
        $_POST[$field] = $application[$field];
    }
}
