<?php

/**
 * @global \REW\Core\Interfaces\User\SessionInterface $user
 */

// Redirect if logged in
if ($user->isValid()) {
    header('Location: /');
    exit;
}

// Page Meta Information
$page_title = Lang::write('IDX_LOGIN_PAGE_TITLE');
$meta_keyw  = Lang::write('IDX_LOGIN_META_KEYWORDS');
$meta_desc  = Lang::write('IDX_LOGIN_META_DESCRIPTION');
$settings   = Settings::getInstance();

// Show Form
$show_form = true;

// Success
$success = array();

// User Errors
$errors = $user->info('errors');
$errors = is_array($errors) ? $errors : array();
$user->saveInfo('errors', false);

// Social Networks
$networks = OAuth_Login::getProviders();

// Process Login
if (isset($_GET['login'])) {
    // Form Data
    $email = trim($_POST['email']);

    // Require Valid Email Address
    $error = Validate::email($email, true) ? '' : 'Please supply a valid email address.';
    if (!empty($error)) {
        $errors[] = $error;
    }
    $user->saveInfo('email', $email);

    // Require Valid Password
    if (!empty($settings->SETTINGS['registration_password'])) {
        $error = Validate::stringRequired($_POST['password']) ? '' : 'Please supply a valid password.';
        if (!empty($error)) {
            $errors[] = $error;
        }
    }

    // Check Errors
    if (empty($errors)) {
        try {
            //Check Failed Login Attempts for users IP in the last minute
            if ($user->getRemainingLoginAttempts() > 0) {
                // DB Connection
                $db = DB::get();

                // Authenticate User and Build Login Token
                if ($user->authenticate($email, $_POST['password'], $db, $settings)) {
                    // Validate User & Login Token
                    $user->validate();

                    // Validate User
                    if ($user->isValid()) {
                        // Log Event: Lead Logged In
                        $event = new History_Event_Action_Login(array(
                            'ip' => $_SERVER['REMOTE_ADDR']
                        ), array(
                            new History_User_Lead($user->user_id())
                        ));

                        // Save to DB
                        $event->save();

                        // MLS Compliance - Require Email Verification
                        if ((!empty($settings->SETTINGS['registration_verify']) && !Validate::verifyWhitelisted($user->info('email'))) || Validate::verifyRequired($user->info('email'))) {
                            if ($user->info('verified') != 'yes') {
                                // Verification URL
                                $user->setRedirectUrl(sprintf($settings->SETTINGS['URL_IDX_VERIFY'], ''));
                            }
                        }

                        // Success!
                        $success[] = 'You have successfully been logged in!';

                        // Hide Form
                        $show_form = false;

                    // Validation Failed
                    } else {
                        throw new Exception_ValidationError('Incorrect login information. Please try again.');
                    }
                } else {
                    throw new Exception_ValidationError('Incorrect login information. Please try again.');
                }
            } else {
                $wait = $user->USER_BAN_LENGTH;
                throw new Exception_ValidationError(sprintf(
                    'There have been too many failed login attempts from your IP, please wait %d %s and try again.',
                    $wait,
                    Format::plural($wait, 'minutes', 'minute')
                ));
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
}

// Auto-Fill User Data
$email = $user->info('email');
