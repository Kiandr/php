<?php

// Set Page Title
$page_title = 'Reset Password';

// Errors
$errors = array();

// Token Error
if (isset($_GET['invalidToken'])) {
    $errors[] = 'Account could not be found. Please try again.';
}

// Process Reminder
if (isset($_GET['remind'])) {
    // Form Data
    $_POST['email'] = trim($_POST['email']);

    // Check & Save Email
    $error = Validate::email($_POST['email']) ? '' : 'Please supply a valid email address.';
    if (!empty($error)) {
        $errors[] = $error;
    }
    $user->saveInfo('email', $_POST['email']);

    // Locate User by Email Address
    $find_user = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE `email` = '" . $db_users->cleanInput($_POST['email']) . "';");
    if (empty($errors) && empty($find_user)) {
        $errors[] = 'No account was found with that email address.';
    }

    // Check Errors
    if (empty($errors)) {
        // Select Auto-Reponder
        $autoresponder = $db_users->fetchQuery("SELECT * FROM `auto_responders` WHERE `id` = 8;");
        if (!empty($autoresponder)) {
            // Setup Mailer
            $mailer = new Backend_Mailer_FormAutoResponder($autoresponder);

            // Set Recipient
            $mailer->setRecipient($find_user['email'], $find_user['first_name'] . ' ' . $find_user['last_name']);

            // Reset URL
            $url = Settings::getInstance()->SETTINGS['URL_IDX'] . 'reset/' . sha1(strtoupper($find_user['email']) . $user->USER_PEPPER . $find_user['password']) . '/';

            // Mailer Tags
            $tags = array(
                'id'          => $find_user['id'],
                'agent'       => $find_user['agent'],
                'first_name'  => $find_user['first_name'],
                'last_name'   => $find_user['last_name'],
                'guid'        => $url
            );

            // Send Email
            if ($mailer->Send($tags)) {
                $db->query("UPDATE `users` SET `timestamp_reset` = NOW()");

                // Success
                $success[] = 'Instructions on how to reset your password have been emailed to you.';

                // Unset
                unset($_POST['email']);

            // Error Occurred
            } else {
                $errors[] = 'Email could not be sent. Please try again.';
            }
        }
    }
}

// Auto-Fill User Data
$email = isset($_GET['remind']) ? $_POST['email'] : $user->info('email');
