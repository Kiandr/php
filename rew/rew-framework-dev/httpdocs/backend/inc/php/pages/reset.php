<?php

// Errors
$errors = array();

// Show Form
$show_form = true;

// Require Token
$token = Format::htmlspecialchars(trim($_GET['token']));

try {
    // DB Connection
    $db = DB::get();

    // Locate Account by Token
    $account = $db->fetch("SELECT "
        . "`a`.`id`, "
        . "`a`.`auth`, "
        . "`auth`.`username`, "
        . "`auth`.`password`, "
        . "CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`"
        . " FROM `" . Auth::$table . "` `auth`"
        . " JOIN `" . $authuser->getTable() . "` `a` ON `auth`.`id` = `a`.`auth`"
        . " WHERE SHA1(CONCAT(UPPER(`auth`.`username`), '" . Auth::AUTH_SALT . "', `auth`.`password`)) = " . $db->quote($token)
        . " AND NOW() - INTERVAL 1 DAY <= `auth`.`timestamp_reset`"
    . ";");

    // Require Account
    if (empty($account)) {
        // Redirect to Remind Form
        header('Location: ' . Settings::getInstance()->URLS['URL_BACKEND'] . 'remind/?invalidToken');
        exit;
    }

    // Process Submit
    if (isset($_GET['submit'])) {
        // Require Password
        $password = trim($_POST['password']);
        if (Validate::stringRequired($password) === false) {
            throw new Exception_ValidationError('Password must not be blank.');
        }

        // Password must be between 6 - 100 Characters
        $length = strlen($password);
        if ($length < 6) {
            throw new Exception_ValidationError('Password cannot be less than 6 characters.');
        }
        if ($length > 100) {
            throw new Exception_ValidationError('Password cannot be more than 100 characters.');
        }

        // Require at least 1 number (0-9)
        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception_ValidationError('Your new password must contain at least 1 number.');
        }

        // Require at least 1 letter (a-zA-Z)
        if (!preg_match('/[a-zA-Z]/', $password)) {
            throw new Exception_ValidationError('Your new password must contain at least 1 letter.');
        }

        // Require at least 1 lower case letter (a-z)
        //if (!preg_match('/[a-z]/', $password)) throw new Exception_ValidationError('For security reasons, your password must contain at least lower case letter: a-z');

        // Require at least 1 upper case letter (A-Z)
        //if (!preg_match('/[A-Z]/', $password)) throw new Exception_ValidationError('For security reasons, your password must contain at least upper case letter: A-Z');

        // Require at least 1 special character (!@#$%^&*)
        //if (!preg_match('/[' . preg_quote('!@#$%^&*()') . ']/', $password)) throw new Exception_ValidationError('For security reasons, your password must contain at least 1 special character: !@#$%^&*()');

        // Confirm Password
        $confirm = trim($_POST['confirm_password']);
        if ($password !== $confirm) {
            throw new Exception_ValidationError('The two passwords must match.');
        }

        //Encrypt new plaintext password
        $encryptedPassword = $authuser->encryptPassword($password);

        // Update Password
        $db->query("UPDATE `" . Auth::$table . "` SET `password` = " . $db->quote($encryptedPassword) . " WHERE `id` = " . $db->quote($account['auth']) . ";");

        //Authenticate User
        $authuser->update($account['username'], $encryptedPassword);

        // Validate User
        if ($authuser->validate()) {
            // If Super Admin, Send New Password to support@realestatewebmasters.com
            if ($authuser->getType() === Auth::TYPE_AGENT && $account['id'] == 1) {
                // Email Message
                $message  = '<p>This is a notification email to inform you that ' . $account['name'] . ' (' . Format::htmlspecialchars($account['username']) . ') has reset their password.</p>' . PHP_EOL;
                $message .= '<p><strong>New Password:</strong> ' . $password . '</p>' . PHP_EOL;
                $message .= '<p><a href="http://hal/pm/index.php?q=' . Http_Host::getDomain() . '">http://hal/pm/index.php?q=' . Http_Host::getDomain() . '</a></p>' . PHP_EOL;
                $message .= '<p style="font-size: 12px; color: #888888;">Please do not reply to this message; it was sent from an unmonitored email address.</p>' . PHP_EOL;

                // Setup Mailer
                $mailer = new Backend_Mailer(array(
                    'subject' => 'Password change notification for ' . Http_Host::getDomain(),
                    'message' => $message
                ));

                // Recipient Information
                $mailer->setRecipient('support@realestatewebmasters.com', 'REW Support');

                // Send Email
                $mailer->Send();
            }

            // Redirect to Dashboard
            header('Location: ' . Settings::getInstance()->URLS['URL_BACKEND']);
            exit;
        } else {
            // Validation Error (This should never occur...)
            $errors[] = 'Error Occurred, Please Contact Support.';
        }
    }

// Validation Error
} catch (Exception_ValidationError $e) {
    $errors[] = $e->getMessage();

// Database Error
} catch (PDOException $e) {
    $errors[] = 'Error Occurred, Please Contact Support.';
    Log::error($e);
}
