<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Associate ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Load Associate
$associate = Backend_Associate::load($_GET['id']);

// Throw Missing Associate Exception
if (empty($associate)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAssociateException();
}

// Get Authorization
$associateAuth = new REW\Backend\Auth\Associates\AssociateAuth($settings, $authuser, $associate);

// Not authorized to view associate history
if (!$associateAuth->canEditAssociate()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit this associate.')
    );
} else {
    // Can edit/delete associate
    $can_edit = true;
    $can_delete = true;
}


// Process Submission
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'username',      'title' => 'Username');
    if ($_POST['update_password']) {
        $required[] = array('value' => 'new_password',      'title' => 'New Password');
        $required[] = array('value' => 'confirm_password',  'title' => 'Confirm Password');
    }
    $required[] = array('value' => 'first_name',    'title' => 'First Name');
    $required[] = array('value' => 'last_name',     'title' => 'Last Name');
    $required[] = array('value' => 'email',         'title' => 'Email Address');

    // Check Required Fields
    foreach ($required as $require) {
        // Trim Whitespace
        $_POST[$require['value']] = Format::trim($_POST[$require['value']]);
        // Require String
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
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
        Auth::validateUsername($_POST['username'], $associate['auth'], $errors);
    }

    // Check Errors
    if (empty($errors)) {
        // Execute Query
        try {
            // Build UPDATE Query
            $query = $db->prepare(
                "UPDATE `associates` SET "
                . "`first_name`			= :first_name, "
                . "`last_name`			= :last_name, "
                . "`email`				= :email, "
                . "`office_phone`		= :office_phone, "
                . "`home_phone`			= :home_phone, "
                . "`cell_phone`			= :cell_phone, "
                . "`fax`			    = :fax, "
                . "`address`		    = :address, "
                . "`city`				= :city, "
                . "`state`				= :state, "
                . "`zip`			    = :zip, "
                . "`timezone`		    = :timezone, "
                . "`default_filter`		= :default_filter, "
                . "`default_order`		= :default_order, "
                . "`default_sort`		= :default_sort, "
                . "`page_limit`			= :page_limit, "
                . "`signature`		    = :signature, "
                . "`add_sig`			= :add_sig, "
                . "`timestamp_updated`	= NOW()"
                . " WHERE `id`          = :id;"
            );

            $query->execute([
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
                "add_sig"           => $_POST['add_sig'],
                "id"                => $associate['id']
            ]);

            // Success
            $success[] = __('Your changes have successfully been saved.');

            // Load Updated Associate
            $associate = Backend_Associate::load($associate['id']);

            //Encrypt provided password.  If this fails abort password/username update
            if ($_POST['update_password']) {
                $password = $authuser->encryptPassword($_POST['new_password']);
            }

            if ($_POST['update_password'] && (empty($password))) {
                $errors[] = __('Username and password could not be updated.');
            } else {
                // Update Username / Password
                try {
                    $query = $db->prepare(
                        "UPDATE `" . Auth::$table . "` SET "
                        . "`username`	= :username"
                        . (($_POST['update_password']) ? ", `password`	= :password":"")
                        . " WHERE `id`	= :id"
                        . ";"
                    );

                    $params = ["username" => $_POST['username'], "id" => $associate['auth']];
                    if ($_POST['update_password']) {
                        $params["password"] = $password;
                    }

                    $query->execute($params);

                    // If Editting Self, Update Username & Password
                    if ($authuser->isAssociate() && $authuser->info('id') == $associate['id']) {
                        $authuser->update($_POST['username'], (($_POST['update_password']) ? $password : null));
                    }

                    //Query Error
                } catch (PDOException $e) {
                    $errors[] = __('An error occurred while updating username and password.');
                }
            }

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ?id=' . $associate['id']);
            exit;

            // Query Error
        } catch (PDOException $e) {
            $errors[] = __('An error occurred while adding saving your changes.');
        }
    } else {
        // Use $_POST Data
        foreach ($associate as $k => $v) {
            $associate[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
        }
    }
}

// Filters
$filters = array(
    array('value' => 'all',         'title' => 'All Leads'),
    array('value' => 'inquiries',   'title' => 'Inquired'),
    array('value' => 'unassigned',  'title' => 'Unassigned'),
    array('value' => 'accepted',        'title' => 'Accepted'),
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

// Load Photo
try {
    $uploads = $db->fetchAll("SELECT `id`, `file` FROM `" . Settings::getInstance()->TABLES['UPLOADS']  . "` WHERE `type` = 'associate' AND `row` = :row ORDER BY `order` ASC LIMIT 1;", ["row" => $associate['id']]);
} catch (PDOException $e) {
    $uploads = [];
}
