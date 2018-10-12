<?php

// Full Page
$body_class = 'full';

// Get Authorization
$lendersAuth = new REW\Backend\Auth\LendersAuth(Settings::getInstance());

// Authorized to Manage Lenders
if (!$lendersAuth->canManageLenders($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view associates.')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Get database, settings, and hooks
$container = Container::getInstance();
$db = $container->get(\REW\Core\Interfaces\DBInterface::class);
$settings = $container->get(\REW\Core\Interfaces\SettingsInterface::class);
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);

// Process Submission
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'username',      'title' => __('Username'));
    $required[] = array('value' => 'password',      'title' => __('Password'));
    $required[] = array('value' => 'first_name',    'title' => __('First Name'));
    $required[] = array('value' => 'last_name',     'title' => __('Last Name'));
    $required[] = array('value' => 'email',         'title' => __('Email Address'));

    // Check Required Fields
    foreach ($required as $require) {
        // Trim Whitespace
        $_POST[$require['value']] = Format::trim($_POST[$require['value']]);
        // Require String
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Require Valid Email Address
    if (!Validate::email($_POST['email'], true)) {
        $errors[] = __('Please supply a valid email address.');
    }

    // Password Validation
    if (!empty($_POST['password'])) {
        try {
            Validate::password($_POST['password']);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    // Validate Username (Check for Duplicates)
    if (!empty($_POST['username'])) {
        Auth::validateUsername($_POST['username'], null, $errors);
    }

    // Check Errors
    if (empty($errors)) {
        //Get Encrypted Password
        $password = $authuser->encryptPassword($_POST['password']);

        $insertParams = [
            'type' => Auth::TYPE_LENDER,
            'username' => $_POST['username'],
            'password' => $password
        ];
        $insertParams['password'] = $password;

        $sqlAuth = "INSERT INTO `" . Auth::$table . "` SET `timestamp_created` = NOW()";
        foreach ($insertParams as $field => $value) {
            $sqlAuth .= ", `" . $field . "` = :" . $field;
        }
        $stmt = $db->prepare($sqlAuth);

        try {
            // Execute Query
            $stmt->execute($insertParams);
        } catch (Exception $e) {
            $errors[] = __('An error occurred while creating new account.');
        }

        if (empty($errors)) {
            // Auth ID
            $auth_id = $db->lastInsertId();

            $insertParams = [
                'auth' => $auth_id,
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'office_phone' => $_POST['office_phone'],
                'home_phone' => $_POST['home_phone'],
                'cell_phone' => $_POST['cell_phone'],
                'fax' => $_POST['fax'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'zip' => $_POST['zip'],
                'default_filter' => $_POST['default_filter'],
                'default_order'  => $_POST['default_order'],
                'default_sort'   => $_POST['default_sort'],
                'timezone' => $_POST['timezone'],
                'page_limit' => $_POST['page_limit'],
                'auto_assign_admin' => $_POST['auto_assign_admin'],
                'auto_assign_optin' => $_POST['auto_assign_admin']
            ];

            // Execute Query
            try {
                $insertParams = $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVE)
                    ->run($insertParams, \REW\Core\Interfaces\Definitions\ModelInterface::LENDER);

                // Build INSERT Query
                $sqlInsert = "INSERT INTO `" . $settings['TABLES']['LM_LENDERS'] . "` SET `auto_assign_time` = NOW(),"
                    . " `timestamp_created` = NOW()";
                foreach ($insertParams as $field => $value) {
                    $sqlInsert .= ", `" . $field . "` = :" . $field;
                }
                $stmtInsert = $db->prepare($sqlInsert);

                $stmtInsert->execute($insertParams);

                // Success
                $success[] = __('Lender has successfully been created.');

                // Insert ID
                $insert_id = $db->lastInsertId();

                // Assign Uploads to Lender
                if (!empty($_POST['uploads'])) {
                    $query = "UPDATE `" . $settings['TABLES']['UPLOADS']  . "` SET `row` = :row WHERE `id` = :upload;";
                    $imageStmt = $db->prepare($query);
                    foreach ($_POST['uploads'] as $upload) {
                        $imageStmt->execute(['row' => $insert_id, 'upload' => $upload]);
                    }
                }

                $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVED)
                    ->run(
                        array_merge(['id' => $insert_id], $insertParams),
                        \REW\Core\Interfaces\Definitions\ModelInterface::LENDER
                    );

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                header('Location: ../lender/edit/?id=' . $insert_id);
                exit;

            // Query Error
            } catch (Exception $e) {
                $errors[] = __('An error occurred while adding new lender.');
            }
        }
    }
}

// Filters
$filters = array(
    array('value' => 'my-leads',    'title' => __('My Leads')),
    array('value' => 'inquiries',   'title' => __('Inquired')),
    array('value' => 'accepted',    'title' => __('Accepted')),
    array('value' => 'pending',     'title' => __('Pending')),
    array('value' => 'online',      'title' => __('Online'))
);

// Order Bys
$orders = array(
    array('value' => 'score',       'title' => __('Score')),
    array('value' => 'value',       'title' => __('Lead Value')),
    array('value' => 'name',        'title' => __('Name')),
    array('value' => 'email',       'title' => __('Email')),
    array('value' => 'status',      'title' => __('Status')),
    array('value' => 'agent',       'title' => __('Agent')),
    array('value' => 'created',     'title' => __('Date/Time Created')),
    array('value' => 'active',      'title' => __('Last Active')),
);

// Order Direction
$sorts = array(
    array('value' => 'DESC',   'title' => __('Descending')),
    array('value' => 'ASC',    'title' => __('Ascending')),
);

// Page Limit
$limits = array(
    array('value' => 10,    'title' => '10'),
    array('value' => 20,    'title' => '20'),
    array('value' => 30,    'title' => '30'),
    array('value' => 50,    'title' => '50'),
    array('value' => 100,   'title' => '100')
);

// Timezones
$timezones = array();
if ($result = $db->query("SELECT `id`, `name`, SEC_TO_TIME(`time_diff`) AS `offset` FROM `" . LM_TABLE_TIMEZONES . "` ORDER BY `time_diff`, `daylight_savings`;")) {
    while ($timezone = $result->fetch()) {
        $timezones[] = array('value' => $timezone['id'], 'title' => $timezone['name'] . ' (GMT ' . substr($timezone['offset'], 0, strpos($timezone['offset'], ':00')) . ')');
    }
}

// Default Timezone
$_POST['timezone'] = isset($_POST['timezone']) ? $_POST['timezone'] : $authuser->info('timezone');

// Lender Photo
$uploads = array();
if (!empty($_POST['uploads'])) {
    $query = "SELECT `id`, `file` FROM `" . $settings['TABLES']['UPLOADS']  . "` WHERE `id` = :upload ORDER BY `order` ASC LIMIT 1;";
    $stmt = $db->prepare($query);
    foreach ($_POST['uploads'] as $upload) {
        $result->execute(['upload' => $upload]);
        while ($row = $result->fetch()) {
            $uploads[] = $row;
        }
    }
}
