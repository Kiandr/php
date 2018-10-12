<?php

// Get DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization Managers
$partnersAuth = new REW\Backend\Auth\PartnersAuth(Settings::getInstance());

// Require Authorization
if (!$partnersAuth->canManageGrasshopper($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage grasshopper integrations')
    );
}

// Partner key
$api_key = 'U8YhUsEDUBe7amaZyjaQa9YbAjUreQUG';

// Partner instance
$api = new Partner_HappyGrasshopper(array(
    'api_key' => $api_key,
));

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
    $_POST['username'] = isset($_POST['username']) ? $_POST['username'] : $authuser->info('partners.grasshopper.username');
    $_POST['password'] = isset($_POST['password']) ? $_POST['password'] : $authuser->info('partners.grasshopper.password');

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
            array('value' => 'username', 'title' => __('Username')),
            array('value' => 'password', 'title' => __('Password')),
        );

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Create request
            if (!($auth = $api->requestAuthentication($_POST['username'], $_POST['password'], $user_key, $user_code))) {
                $errors[] = __('Happy Grasshopper&reg; returned an error: %s', $api->getLastError());
                return;
            }

            // Require keys
            if (empty($user_key) || empty($user_code)) {
                $errors[] = __('Failed to obtain authentication details from Happy Grasshopper&reg;. Please try again later.');
                return;
            }

            // Current partners
            $partners = $authuser->info('partners');

            // Merge changes
            $partners = array_merge($partners, array(
                'grasshopper' => array(
                    'api_key' => $api_key,
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'user_key' => $user_key,
                    'user_code' => $user_code,
                ),
            ));

            try {
                // Build query
                $query = $db->prepare(
                    "UPDATE `agents` SET `partners` = :partner WHERE `id` = :id;"
                );
                $result = $query->execute(
                    [
                        'partner' => json_encode($partners),
                        'id' => $authuser->info('id')
                    ]
                );
            } catch(PDOException $e) {
                $errors[] =  __('Error updating partner settings.');
            }

            if ($result) {

                // Create group if needed
                try {
                    $group = $db->fetchAll(
                        "SELECT `id` FROM `groups` WHERE `name` = :name AND `agent_id` IS NULL AND `user` = 'false' LIMIT 1;",
                        ['name' => Partner_HappyGrasshopper::GROUP_NAME]
                    );
                } catch(PDOException $e) {
                    $errors[] =  __('Error accessing group information.');
                }
                if (empty($group)) {
                    try {
                        $query = $db->prepare("INSERT INTO `groups` SET "
                            . "`name`			= :name, "
                            . "`description`	= :description, "
                            . "`style`			= :style, "
                            . "`user`			= 'false', "
                            . "`agent_id`		= NULL;"
                        );
                        $query->execute([
                            'name' => Partner_HappyGrasshopper::GROUP_NAME,
                            'description' => 'Leads in this group will be synced with Happy Grasshopper',
                            'style' => Partner_HappyGrasshopper::GROUP_STYLE
                        ]);
                    } catch (PDOException $e) {
                        $errors[] = __('Error creating Happy Grasshopper group.');
                    }
                }

                // Success
                if(empty($errors)) {
                    $success[] = __('Your changes have successfully been saved.');
                }

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect
                header('Location: ?');
                exit;
            }
        }
    }
}

// API User Key
$user_key = $authuser->info('partners.grasshopper.user_key');
$user_code = $authuser->info('partners.grasshopper.user_code');

// Overview mode
if (!isset($_GET['setup']) && !empty($user_key) && !empty($user_code)) {
    // Set options for future requests
    $api->setOptions(array(
        'user_key' => $user_key,
        'user_code' => $user_code,
    ));

    // Get signature
    if (!($signature_response = $api->getUserSignature($signature))) {
        $errors[] = __('Failed to obtain signature: %s', $api->getLastError());
    }

    // Form fields
    $fields = array(
        array(
            'source' => $signature_response,
            'prefix' => 'signature_',
            'fields' => array(
                'name', 'business_name', 'signed', 'tag_line', 'email', 'phone', 'phone_2',
                'fax', 'website', 'website_2', 'street_address', 'city', 'state', 'zip', 'facebook_profile',
                'facebook_page', 'google_plus', 'youtube', 'pinterest', 'linkedin', 'twitter', 'dataid',
            ),
        ),
    );

    // Set $_POST defaults
    foreach ($fields as $field_config) {
        $source = $field_config['source'];
        $source = array_change_key_case($source[0]);
        if (empty($source)) {
            continue;
        }

        // Set source fields
        $prefix = $field_config['prefix'];
        foreach ($field_config['fields'] as $field_name) {
            $post_field_name = $prefix . $field_name;
            if (!isset($_POST[$post_field_name])) {
                $_POST[$post_field_name] = $source[$field_name];
            }
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
            array('value' => 'signature_signed',            'title' => __('Signed')),
            array('value' => 'signature_name',              'title' => __('Full Name')),
            array('value' => 'signature_street_address',    'title' => __('Address')),
            array('value' => 'signature_city',              'title' => __('City')),
            array('value' => 'signature_state',             'title' => __('State')),
            array('value' => 'signature_zip',               'title' => __('ZIP')),
            array('value' => 'signature_email',             'title' => __('Email')),
        );

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Signature fields
            $signature_fields = array();
            $post_prefix = 'signature_';
            foreach ($fields as $field_config) {
                if ($field_config['prefix'] !== $post_prefix) {
                    continue;
                }
                foreach ($field_config['fields'] as $field_name) {
                    $post_field_name = $post_prefix . $field_name;
                    if (isset($_POST[$post_field_name])) {
                        $signature_fields[$field_name] = $_POST[$post_field_name];
                    }
                }
            }

            // Fix field names due to differences between HG's outgoing and incoming API
            $signature_fields['data_id'] = $signature_fields['dataid'];
            $signature_fields['address'] = $signature_fields['street_address'];
            $signature_fields['phone2'] = $signature_fields['phone_2'];
            $signature_fields['linked_in'] = $signature_fields['linkedin'];
            unset($signature_fields['dataid']);
            unset($signature_fields['street_address']);
            unset($signature_fields['phone_2']);
            unset($signature_fields['linkedin']);

            // Update signature request
            if (!($update_signature = $api->updateUserSignature($signature_fields))) {
                $errors[] = __('Email signature could not be updated: %s', $api->getLastError());
            }

            // Check success
            if (empty($errors)) {
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
