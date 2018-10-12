<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageBombomb($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage bombomb integrations')
    );
}

// Partner instance
$api = new Partner_BombBomb();

// Form action
$form_action = '?';
if (isset($_GET['setup'])) {
    $form_action = '?setup';
}

// Success
$success = array();

// Errors
$errors = array();

// Setup mode
if (isset($_GET['setup'])) {
    // Defaults
    $_POST['api_key'] = isset($_POST['api_key']) ? $_POST['api_key'] : $authuser->info('partners.bombbomb.api_key');

    // Form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Trim input
        foreach ($_POST as $k => $v) {
            if (is_string($v)) {
                $_POST[$k] = trim($v);
            }
        }

        // Required Fields
        $required   = array(
            array('value' => 'api_key', 'title' => __('API Key')),
        );

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Set API credentials
            $api->setOptions(array(
                'api_key' => $_POST['api_key'],
            ));

            // Test validity
            $lists = $api->getLists();
            if ($err = $api->getLastError()) {
                $errors[] = __('The provided BombBomb API key is invalid.');
                return;
            }

            // Current partners
            $partners = $authuser->info('partners');

            // Find existing List
            $list_id = null;
            if ($lists = $api->getLists()) {
                foreach ($lists as $list) {
                    if ($list['name'] === Partner_BombBomb::LIST_NAME) {
                        $list_id = $list['id'];
                        break;
                    }
                }
            }

            // Create List
            if (empty($list_id)) {
                if ($create_list = $api->createList(Partner_BombBomb::LIST_NAME)) {
                    if (!empty($create_list['id'])) {
                        $list_id = $create_list['id'];
                    }
                }
            }

            // Merge changes
            $partners = array_merge($partners, array(
                'bombbomb' => array(
                    'api_key' => $_POST['api_key'],
                    'list_id' => $list_id,
                ),
            ));

            // Build query
            $sql = "UPDATE `agents` SET `partners` = '" . mysql_real_escape_string(json_encode($partners)) . "' WHERE `id` = '" . mysql_real_escape_string($authuser->info('id')) . "';";
            if (mysql_query($sql)) {
                // Create group if needed
                $sql = "SELECT `id` FROM `groups` WHERE `name` = '" . mysql_real_escape_string(Partner_BombBomb::GROUP_NAME) . "' AND `agent_id` IS NULL AND `user` = 'false';";
                $group = mysql_fetch_assoc(mysql_query($sql));
                if (empty($group)) {
                    $sql = "INSERT INTO `groups` SET "
                            . "`name`			= '" . mysql_real_escape_string(Partner_BombBomb::GROUP_NAME) . "', "
                            . "`description`	= 'Leads in this group will be synced with BombBomb', "
                            . "`style`			= '" . mysql_real_escape_string(Partner_BombBomb::GROUP_STYLE) . "', "
                            . "`user`			= 'false', "
                            . "`agent_id`		= NULL;";
                    mysql_query($sql);
                }

                // Success
                $success[] = __('Your changes have successfully been saved.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect
                header('Location: ?');
                exit;
            }
        }
    }
}

// API integration status
$logins_valid = false;
$api_key = $authuser->info('partners.bombbomb.api_key');
if (!empty($api_key)) {
    // Set options
    $api->setOptions(array(
        'api_key' => $api_key,
    ));

    // Test validity
    $lists = $api->getLists();
    if (!($err = $api->getLastError())) {
        $logins_valid = true;
    }
}

// Overview mode
if (!isset($_GET['setup']) && !empty($logins_valid)) {
    // Get Lists
    if (!($lists = $api->getLists())) {
        $errors[] = __('Failed to obtain Lists from BombBomb: %s', $api->getLastError());
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
            array('value' => 'list_id', 'title' => __('Destination List')),
        );

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Current partners
            $partners = $authuser->info('partners');

            // Update values
            $partners['bombbomb']['list_id'] = $_POST['list_id'];

            // Build query
            $sql = "UPDATE `agents` SET `partners` = '" . mysql_real_escape_string(json_encode($partners)) . "' WHERE `id` = '" . mysql_real_escape_string($authuser->info('id')) . "';";
            if (mysql_query($sql)) {
                // Success
                $success[] = __('Your changes have successfully been saved.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect
                header('Location: ?');
                exit;
            }
        }
    }
}
