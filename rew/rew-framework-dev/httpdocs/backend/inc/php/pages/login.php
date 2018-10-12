<?php

// Errors
$errors = array();

// Handle RE/MAX Integra's Launchpad SSO Requests
include('remax_sso_login.php');

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

        // Require Password
        $password = $_POST['password'];
        if (!Settings::isREW() && Validate::stringRequired($password) === false) {
            throw new Exception_ValidationError('Password is a required field.');
        }

        // Set Cookie
        $authuser->setCookie(!empty($_POST['remember']) ? true : false);

        // Check Failed Login Attempts for users IP in the last minute
        if ($authuser->getRemainingLoginAttempts() <= 0) {
            $wait = $authuser->USER_BAN_LENGTH;
            throw new Exception_ValidationError(sprintf(
                'There have been too many failed login attempts from your IP, please wait %d %s and try again.',
                $wait,
                Format::plural($wait, 'minutes', 'minute')
            ));
        }

        // Authenticate User and Build Login Token
        if (!$authuser->authenticate($username, $password, $db)) {
            throw new Exception_ValidationError('Incorrect username or password! Please try again.');
        }

        // Validate User & Login Token
        $authuser->validate($db);

        // If Not Validate User
        if (!$authuser->isValid()) {
            throw new Exception_ValidationError('Invalid username or password! Please try again.');
        }

        // Log Event: Agent Logged In
        $event = new History_Event_Action_Login(array(
            'ip' => $_SERVER['REMOTE_ADDR']
        ), array(
            $authuser->getHistoryUser()
        ));

        // Save to DB
        $event->save();

        // Redirect URL
        $redirect = Settings::getInstance()->URLS['URL_BACKEND'];
        if(!empty($_SESSION['redirect']) && strpos($_SESSION['redirect'], '/ajax/') === false){
            $redirect = $_SESSION['redirect'];
        }
        unset($_SESSION['redirect']);

        // Redirect
        header('Location: ' . $redirect);
        exit;

    // Validation Error
    } catch (Exception_ValidationError $e) {
        $errors[] = $e->getMessage();

    // Database Error
    } catch (PDOException $e) {
        $errors[] = 'Error Occurred, Please Contact Support.';
        Log::error($e);
    }
}
