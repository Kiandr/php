<?php

// Success
$success = array();

// Errors
$errors = array();

// Show Form
$show_form = true;

// Token Error
if (isset($_GET['invalidToken'])) {
    $errors[] = 'Account could not be found. Please try again.';
}

// Process Submit
if (isset($_GET['submit'])) {
    try {
        // DB Connection
        $db = DB::get();

        // Require Username
        $username = trim($_POST['username']);
        if (Validate::stringRequired($username) === false) {
            throw new Exception_ValidationError('Username is a required field.');
        }

        // Locate Account
        $account = $db->fetch("SELECT "
            . "`auth`.`username`, "
            . "`auth`.`password`, "
            . "CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`, "
            . "`a`.`email`"
            . " FROM `" . Auth::$table . "` `auth`"
            . " JOIN `" . $authuser->getTable() . "` `a` ON `auth`.`id` = `a`.`auth`"
            . " WHERE UPPER(`username`) = " . $db->quote(strtoupper($username))
         . ";");

        // Require Account
        if (empty($account)) {
            throw new Exception_ValidationError('Account could not be found: ' . Format::htmlspecialchars($username));
        }

        // Reset URL
        $url = Settings::getInstance()->URLS['URL_BACKEND'] . 'reset/' . sha1(strtoupper($account['username']) . Auth::AUTH_SALT . $account['password']) . '/';

        // Email Message
        $message  = '<p>Forgot your password, ' . Format::htmlspecialchars($account['name']) . '?</p>' . PHP_EOL;
        $message .= '<p>We\'ve received a request to reset the password for your account (' . Format::htmlspecialchars($account['username']) . ').</p>' . PHP_EOL;
        $message .= '<p>To reset your password, click on the link below (or copy and paste the URL into your browser):</p>' . PHP_EOL;
        $message .= '<p><a href="' . $url . '">' . $url . '</a></p>' . PHP_EOL;
        $message .= '<p>If you didn\'t initiate this request, you don\'t need to take any further action and can safely disregard this email. </p>' . PHP_EOL;
        $message .= '<p>REW Support</p>' . PHP_EOL;
        $message .= '<p style="font-size: 12px; color: #888888;">Please do not reply to this message; it was sent from an unmonitored email address. For general inquiries or to request support with your REW account, please visit us at <a href="http://www.realestatewebmasters.com/csr/getting-started.php">REW Support</a>.</p>' . PHP_EOL;

        // Setup Mailer
        $mailer = new Backend_Mailer(array(
            'subject' => 'Reset your password',
            'message' => $message
        ));

        // Recipient Information
        $mailer->setRecipient($account['email'], $account['name']);

        // Send Email
        if ($mailer->Send()) {
            $db->query("UPDATE `auth` SET `timestamp_reset` = NOW()");

            // Success
            $success[] = 'Password reset instructions have been sent to your email address.';

            // Hide Form
            $show_form = false;

        // Mailer Error
        } else {
            $errors[] = 'Error occurred while sending Password reset instructions.';
        }

    // Validation Error
    } catch (Exception_ValidationError $e) {
        $errors[] = $e->getMessage();

    // Database Error
    } catch (PDOException $e) {
        $errors[] = 'Error Occurred, Please Contact Support.';
        Log::error($e);
    }
}
