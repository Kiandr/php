<?php

// Key Passed in URL, Auto Verify
if (!empty($_GET['verify'])) {
    $_GET['verify'] = trim($_GET['verify']);
    if (Validate::guid($_GET['verify'])) {
        $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE `guid` = GuidToBinary('" . $db_users->cleanInput($_GET['verify']) . "') LIMIT 1;");
    } else if (Validate::sha1($_GET['verify'])) {
        $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE SHA1(UPPER(`email`)) = '" . $db_users->cleanInput($_GET['verify']) . "' LIMIT 1;");
    }
    if (!empty($lead)) {
        $_POST['step']  = 'verify';
        $_POST['email'] = $lead['email'];
        $_POST['code']  = Format::toGuid($lead['guid']);
        $_GET['submit'] = "true";
    }
}

// Set Page Title
$page_title = 'Verify Your Email Address';

// Show Form
$show_form = true;

// Success
$success = array();

// Errors
$errors  = array();

// Lead Email
$_POST['email'] = (isset($_GET['submit']) && isset($_POST['email'])) ? $_POST['email'] : $user->info('email');

// Verify Email Address
if (isset($_GET['submit']) && ($_POST['step'] == 'verify')) {
    // Trim Code
    $_POST['code'] = Format::trim($_POST['code']);

    // Require Valid Verification Code
    if (!Validate::guid($_POST['code']) && !Validate::sha1($_POST['code'])) {
        $errors[] = 'Please enter in the validation code that was emailed to you.';
    } else {
        // Find Lead using Code
        $_POST['code'] = trim($_POST['code']);
        if (Validate::guid($_POST['code'])) {
            $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE `guid` = GuidToBinary('" . $db_users->cleanInput($_POST['code']) . "') LIMIT 1;");
        } else if (Validate::sha1($_POST['code'])) {
            $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE SHA1(UPPER(`email`)) = '" . $db_users->cleanInput($_POST['code']) . "' LIMIT 1;");
        }
        if (empty($lead)) {
            $errors[] = 'There was an error with the validation code you entered.';
        }
    }

    // Check Errors & Row
    if (empty($errors)) {
        if ($lead['verified'] != 'yes') {
            // Verify Lead
            $query = "UPDATE `" . TABLE_USERS . "` SET `verified` = 'yes' WHERE `id` = '" . $lead['id'] . "'";
            if ($db_users->query($query)) {
                // Set User ID Information
                $user->setUserId($lead['id']);

                // Validate User
                $user->validate();

                // Lead is Verified
                $user->saveInfo('verified', 'yes');

                // Get Assigned Agent
                $agent = $db_users->fetchQuery("SELECT * FROM `" . TABLE_AGENTS . "` WHERE `id` = '" . $db_users->cleanInput($lead['agent']) . "' LIMIT 1;");

                // Create \PHPMailer\RewMailer
                $mailer = new \PHPMailer\RewMailer();
                $mailer->CharSet = 'UTF-8';
                $mailer->IsHTML(true);

                // Sender
                $mailer->From = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];
                $mailer->FromName = $lead['first_name'] . ' ' . $lead['last_name'];

                // Reply-To
                $mailer->AddReplyTo($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

                // Recipient
                $mailer->AddAddress($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);

                // Email Subject
                $mailer->Subject = 'IDX Registration Submission [Verified]';

                // Email Message
                $mailer->Body .= '<p><b>' . $lead['first_name'] . ' ' . $lead['last_name'] . '</b> has verified their email address: <a href="mailto:' . $lead['email'] . '">' . $lead['email'] . '</a></p>';
                $mailer->Body .= '<p>The following information has been collected about the user:</p>';
                $mailer->Body .= $user->formatUserInfo();

                // Send Email
                $mailer->Send();

                // Log Event: Lead has Verified Email Address
                $event = new History_Event_Action_Verified(null, array(
                    new History_User_Lead($lead['id'])
                ));

                // Save to DB
                $event->save();

                // Hide Form
                $show_form = false;
            } else {
                // Query Error
                $errors[] = 'We\'re sorry, but an error has occurred. Please try again.';
            }

        // User already verified, sign them in
        } else {
            // Set User ID Information
            $user->setUserId($lead['id']);

            // Validate User
            $user->validate();

            // Hide Form
            $show_form = false;
        }
    }
}

// Email Verification Code
if (isset($_GET['submit']) && ($_POST['step'] == 'email')) {
    // Require checkbox to be checked
    if (empty($_POST['resend'])) {
        $errors[] = 'You must check the checkbox to re-send your confirmation link.';
    }

    // Require Valid Email Address
    if (!Validate::email($_POST['email'], true)) {
        $errors[] = 'Please supply a valid email address.';
    } else {
        // Locate Lead by Email
        $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE `email` = '" . $db_users->cleanInput($_POST['email']) . "';");
        if (empty($lead)) {
            $errors[] = 'The email account you entered is not registered.';
        }
    }

    // Check Errors & Rows
    if (empty($errors) && !empty($lead)) {
        // Setup Verification Mailer
        $mailer = new Backend_Mailer_Verification(array(
            'lead' => $lead
        ));

        // Send Email
        if ($mailer->Send()) {
            // Success
            $success[] = 'Your verification code has successfully been sent to you at <strong>' . htmlspecialchars($lead['email']) . '</strong>.';
            $success[] = 'If you don\'t see the e-mail in your inbox, please check your spam box as well.';

        // Mailer Error
        } else {
            $errors[] = 'An error occurred, Verification Code could not be sent.';
        }
    }
}
