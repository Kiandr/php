<?php

// Get Authorization
$lendersAuth = new REW\Backend\Auth\LendersAuth(Settings::getInstance());

// Can Edit & Delete
$can_edit = $lendersAuth->canManageLenders($authuser);
$can_delete = $lendersAuth->canDeleteLenders($authuser);

// Require permission to edit all lenders
if (!$lendersAuth->canManageLenders($authuser)) {
    // Require permission to edit self
    if (!$lendersAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit lenders.')
        );
    } else {
        // Row ID
        $_GET['id'] = $authuser->info('id');
    }
} else {
    // Row ID
    $_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

    // Can Delete
    $can_delete = true;
}

// Success
$success = array();

// Errors
$errors = array();

// Load Lender
$lender = Backend_Lender::load($_GET['id']);

/* Throw Missing ID Exception */
if (empty($lender)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLenderException();
}

// Get database, settings, and hooks
$container = Container::getInstance();
$db = $container->get(\REW\Core\Interfaces\DBInterface::class);
$settings = $container->get(\REW\Core\Interfaces\SettingsInterface::class);
$hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);

// Require Lender
if (!empty($lender)) {
    // Process Submission
    if (isset($_GET['submit'])) {
        // Required Fields
        $required   = array();
        $required[] = array('value' => 'username',      'title' => __('Username'));
        if ($_POST['update_password']) {
            $required[] = array('value' => 'new_password',      'title' => __('New Password'));
            $required[] = array('value' => 'confirm_password',  'title' => __('Confirm Password'));
        }
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
        if (!empty($_POST['new_password'])) {
            try {
                Validate::password($_POST['new_password']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Validate Username (Check for Duplicates)
        if (!empty($_POST['username'])) {
            Auth::validateUsername($_POST['username'], $lender['auth'], $errors);
        }

        // Check Errors
        if (empty($errors)) {
            $updateParams = [];

            // Lender Updated by Admin
            $query_extras = '';
            if ($lendersAuth->canManageLenders($authuser)) {
                $updateParams['auto_assign_admin'] = $_POST['auto_assign_admin'];
                // Opted-In, Update Auto-Assign Time
                if ($_POST['auto_assign_admin'] == 'true' && $_POST['auto_assign_admin'] != $lender['auto_assign_admin']) {
                    $updateParams['auto_assign_time'] = date('Y-m-d H:i:s');
                }
            // Lender Opted-In, Update Auto-Assign Time
            } elseif ($_POST['auto_assign_optin'] == 'true' && $_POST['auto_assign_optin'] != $lender['auto_assign_optin']) {
                $updateParams['auto_assign_time'] = date('Y-m-d H:i:s');
            }

            $updateParams = array_merge($updateParams, [
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
                'auto_assign_optin' => $_POST['auto_assign_optin'],
                'id' => $lender['id']
            ]);

            try {
                $updateParams = $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVE)
                    ->run($updateParams, \REW\Core\Interfaces\Definitions\ModelInterface::LENDER);

                $sqlUpdate = "UPDATE `" . $settings['TABLES']['LM_LENDERS'] . "` SET `timestamp_updated` = NOW()";
                foreach ($updateParams as $field => $value) {
                    $sqlUpdate .= ", `" . $field . "` = :" . $field;
                }
                $sqlUpdate .= " WHERE `id` = :id";
                $stmtUpdate = $db->prepare($sqlUpdate);

                $stmtUpdate->execute($updateParams);

                // Success
                $success[] = __('Your changes have successfully been saved.');

                $hooks->hook(\REW\Core\Interfaces\HooksInterface::HOOK_ON_SAVED)
                    ->run($updateParams, \REW\Core\Interfaces\Definitions\ModelInterface::LENDER);

                // Load Updated Lender
                $lender = Backend_Lender::load($lender['id']);

                //Encrypt provided password.  If this fails abort password/username update
                if ($_POST['update_password']) {
                    $password = $authuser->encryptPassword($_POST['new_password']);
                }

                if ($_POST['update_password'] && (empty($password))) {
                    $errors[] = __('Username and password could not be updated.');
                } else {
                    $updateParams = [
                        'username' => $_POST['username'],
                        'id' => $lender['auth']
                    ];
                    if ($_POST['update_password']) {
                        $updateParams['password'] = $password;
                    }

                    $sqlAuth = "UPDATE `" . Auth::$table . "` SET ";
                    foreach ($updateParams as $field => $value) {
                        $sqlAuth .= "`" . $field . "` = :" . $field . ", ";
                    }
                    $sqlAuth = rtrim($sqlAuth, ", ") . " WHERE `id` = :id";
                    $stmt = $db->prepare($sqlAuth);

                    try {
                        $stmt->execute($updateParams);

                        // If Editing Self, Update Username & Password
                        if ($authuser->isLender() && $authuser->info('id') == $lender['id']) {
                            $authuser->update($_POST['username'], (($_POST['update_password']) ? $password : null));
                        }
                    } catch (Exception $e) {
                        $errors[] = __('An error occurred while updating username %s.', (($_POST['update_password']) ? __('and password') : '' ));
                    }
                }

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                header('Location: ?id=' . $lender['id']);
                exit;

            // Query Error
            } catch (Exception $e) {
                $errors[] = __('An error occurred while adding saving your changes.');
            }
        } else {
            // Use $_POST Data
            foreach ($lender as $k => $v) {
                $lender[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
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

    // Lender Photo
    $query = "SELECT `id`, `file` FROM `" . $settings['TABLES']['UPLOADS']  . "` WHERE `type` = 'lender' AND `row` = :id ORDER BY `order` ASC LIMIT 1;";
    $uploads = $db->fetchAll($query, ['id' => $lender['id']]);
}
