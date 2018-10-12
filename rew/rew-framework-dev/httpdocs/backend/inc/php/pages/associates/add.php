<?php

// Full Page
$body_class = 'full';

// Get Authorization
$associateAuth = new REW\Backend\Auth\AssociateAuth(Settings::getInstance());

// Authorized to Manage Associates
if (!$associateAuth->canManageAssociates($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view associates.')
    );
}

// Success
$success = array();

// Errors
$errors = array();

$db = DB::get();

// Process Submission
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'username',      'title' => 'Username');
    $required[] = array('value' => 'password',      'title' => 'Password');
    $required[] = array('value' => 'first_name',    'title' => 'First Name');
    $required[] = array('value' => 'last_name',     'title' => 'Last Name');
    $required[] = array('value' => 'email',         'title' => 'Email Address');

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

        // Execute Query
        try {
            // Insert Auth Account
            $auth_insert = $db->prepare(
                "INSERT INTO `" . Auth::$table . "` SET "
                . "`type`				= :type, "
                . "`username`			= :username, "
                . "`password`			= :password, "
                . "`timestamp_created`	= NOW();"
            );

            $auth_insert->execute([
                "type" => Auth::TYPE_ASSOCIATE,
                "username" => $_POST['username'],
                "password" => $password
            ]);
            // Auth ID
            $auth_id = $db->lastInsertId();

            // Execute Query
            try {
                // Build INSERT Query
                $query = $db->prepare(
                    "INSERT INTO `associates` SET "
                    . "`auth`				= :auth, "
                    . "`first_name`	        = :first_name, "
                    . "`last_name`		    = :last_name, "
                    . "`email`				= :email, "
                    . "`office_phone` 	    = :office_phone, "
                    . "`home_phone`		    = :home_phone, "
                    . "`cell_phone`		    = :cell_phone, "
                    . "`fax`				= :fax, "
                    . "`address`			= :address, "
                    . "`city`			  	= :city, "
                    . "`state`			  	= :state, "
                    . "`zip`				= :zip, "
                    . "`timezone`		 	= :timezone, "
                    . "`default_filter`     = :default_filter, "
                    . "`default_order`	    = :default_order, "
                    . "`default_sort`	    = :default_sort, "
                    . "`page_limit`		    = :page_limit, "
                    . "`signature`			= :signature, "
                    . "`add_sig`		    = :add_sig, "
                    . "`timestamp_created`	= NOW();"
                );

                $query->execute([
                    "auth"              => $auth_id,
                    "first_name"        => $_POST['first_name'],
                    "last_name"         => $_POST['last_name'],
                    "email"             => $_POST['email'],
                    "office_phone"      => $_POST['office_phone'],
                    "home_phone"        => $_POST['home_phone'],
                    "cell_phone"        => $_POST['cell_phone'],
                    "fax"               => $_POST['fax'],
                    "address"           => $_POST['address'],
                    "city"              => $_POST['city'],
                    "state"             => $_POST['state'],
                    "zip"               => $_POST['zip'],
                    "timezone"          => $_POST['timezone'],
                    "default_filter"    => $_POST['default_filter'],
                    "default_order"     => $_POST['default_order'],
                    "default_sort"      => $_POST['default_sort'],
                    "page_limit"        => $_POST['page_limit'],
                    "signature"         => $_POST['signature'],
                    "add_sig"           => $_POST['add_sig']
                ]);

                // Success
                $success[] = __('ISA has successfully been created.');

                // Insert ID
                $insert_id = $db->lastInsertId();

                // Assign Uploads to Associate
                if (!empty($_POST['uploads'])) {
                    foreach ($_POST['uploads'] as $upload) {
                        try {
                            $query = $db->prepare("UPDATE `" . Settings::getInstance()->TABLES['UPLOADS'] . "` SET `row` = :row WHERE `id` = :id;");
                            $query->execute([
                                "row" => $insert_id,
                                "id" => $upload
                            ]);
                        } catch (PDOException $e) {
                        }
                    }
                }

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                header('Location: ../associate/edit/?id=' . $insert_id);
                exit;

            // Query Error
            } catch (PDOException $e) {
                $errors[] = __('An error occurred while adding new ISA.');
            }
        } catch (PDOException $e) {
            $errors[] = __('An error occurred while creating new account.');
        }
    }
}

// Filters
$filters = array(
    array('value' => 'all',         'title' => 'All Leads'),
    array('value' => 'inquiries',   'title' => 'Inquired'),
    array('value' => 'unassigned',  'title' => 'Unassigned'),
    array('value' => 'accepted',    'title' => 'Accepted'),
    array('value' => 'pending',     'title' => 'Pending'),
    array('value' => 'online',      'title' => 'Online'),
);

// Order Bys
$orders = array(
    array('value' => 'score',       'title' => 'Score'),
    array('value' => 'value',       'title' => 'Lead Value'),
    array('value' => 'name',        'title' => 'Name'),
    array('value' => 'email',       'title' => 'Email'),
    array('value' => 'status',      'title' => 'Status'),
    array('value' => 'agent',       'title' => 'Agent'),
    array('value' => 'lender',       'title' => 'Lender'),
    array('value' => 'created',     'title' => 'Date/Time Created'),
    array('value' => 'active',      'title' => 'Last Active'),
);

// Order Direction
$sorts = array(
    array('value' => 'DESC',   'title' => 'Descending'),
    array('value' => 'ASC',    'title' => 'Ascending'),
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
try {
    foreach ($db->fetchAll("SELECT `id`, `name`, SEC_TO_TIME(`time_diff`) AS `offset` FROM `" . LM_TABLE_TIMEZONES . "` ORDER BY `time_diff`, `daylight_savings`;") as $timezone) {
        $timezones[] = array('value' => $timezone['id'], 'title' => $timezone['name'] . ' (GMT ' . substr($timezone['offset'], 0, strpos($timezone['offset'], ':00')) . ')');
    }
} catch (PDOException $e) {
}

// Default Timezone
$_POST['timezone'] = isset($_POST['timezone']) ? $_POST['timezone'] : $authuser->info('timezone');

// Load Photo
$uploads = array();
if (!empty($_POST['uploads'])) {
    try {
        foreach ($_POST['uploads'] as $upload) {
            $uploads[] = $db->fetch("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = :id ORDER BY `order` ASC LIMIT 1;", ["id" => $upload]);
        }
    } catch (PDOException $e) {
    }
}
