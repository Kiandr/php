<?php

// Set Page Title
$page_title = 'Reset Password';

// Errors
$errors = array();

// Require Token
$token = Format::htmlspecialchars(trim($_GET['token']));

try {
    // DB Connection
    $db = DB::get();

    // Locate Account by Token
    $account = $db->fetch("SELECT "
        . "`id`, "
        . "`guid`, "
        . "`email`, "
        . "`password` "
        . " FROM `users`"
        . " WHERE SHA1(CONCAT(UPPER(`users`.`email`), '" . $user->USER_PEPPER . "', `users`.`password`)) = " . $db->quote($token)
        . " AND NOW() - INTERVAL 1 DAY <= `users`.`timestamp_reset`"
    . ";");

    // Require Account
    if (empty($account)) {
        // Redirect to Remind Form
        header('Location: ' . Settings::getInstance()->SETTINGS['URL_IDX'] . 'remind.html?invalidToken');
        exit;
    }

    // Process Reset Request
    if (isset($_GET['reset'])) {
        // Require Password
        $password = trim($_POST['password']);
        if (Validate::stringRequired($password) === false) {
            throw new Exception_ValidationError('Password must not be blank.');
        }

        // Confirm Password
        $confirm = trim($_POST['confirm_password']);
        if ($password !== $confirm) {
            throw new Exception_ValidationError('The two passwords must match.');
        }

        // Validate password
        if (!empty($password)) {
            try {
                Validate::password($password);
            } catch (Exception $e) {
                throw new Exception_ValidationError($e->getMessage());
            }
        }

        //Encrypt new plaintext password
        $encryptedPassword = $user->encryptPassword($password);

        // Update Password
        $db->query("UPDATE `users` SET `password` = " . $db->quote($encryptedPassword) . " WHERE `id` = " . $db->quote($account['id']) . ";");

        //Authenticate User
        $user->setUserId($account['id']);
        $user->update($account['email'], $encryptedPassword);

        // Validate User
        if ($user->validate()) {
            // Redirect to Dashboard
            header('Location: ' . Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']);
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
