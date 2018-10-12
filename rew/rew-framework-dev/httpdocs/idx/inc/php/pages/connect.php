<?php

// MLS Compliance
global $_COMPLIANCE;

// Page Meta Information
$page_title = Lang::write('IDX_CONNECT_PAGE_TITLE');
$meta_keyw  = Lang::write('IDX_CONNECT_META_KEYWORDS');
$meta_desc  = Lang::write('IDX_CONNECT_META_DESCRIPTION');

// Show Form
$show_form = true;

// Anti-Spam Email
$anti_spam = array(
        'optin' => Settings::getInstance()->LANG == 'en-CA' ? false : Settings::get('anti_spam.optin'),
        'consent_text' => Settings::get('anti_spam.consent_text')
);

// Anti-Spam Text
$anti_spam_sms = array(
        'optin' => Settings::getInstance()->LANG == 'en-CA' ? false : Settings::get('anti_spam_sms.optin'),
        'consent_text' => Settings::get('anti_spam_sms.consent_text')
);

// Success
$success = $user->info('success');
$success = is_array($success) ? $success : array();
$user->saveInfo('success', false);

// Errors
$errors = $user->info('errors');
$errors = is_array($errors) ? $errors : array();
$user->saveInfo('errors', false);

// Connected via Social Media
$connected = $user->info('connected');
if (!empty($connected)) {
    // Social Provider
    $provider = $connected['provider'];

    // Connected
    if (!isset($_GET['submit']) && empty($success)) {
        $success[] = 'You have successfully connected via <strong>' . $provider . '</strong>.';
    }

    // DB Field to Store Profile Data
    $oauth_profile = $connected['store_profile'];

    // DB Field to Store OAuth Token
    $oauth_token = $connected['store_token'];

    // Profile Details
    $profile = $connected['profile'];

    // OAuth Token
    $token = $connected['token'];

    // Process Submit
    if (isset($_GET['submit'])) {
        // Require First Name
        if (isset($_POST['onc5khko'])) {
            $_POST['first_name'] = $_POST['onc5khko'];
        }
        $error = Validate::stringRequired($_POST['first_name']) ? '' : 'Please supply your first name.';
        if (!empty($error)) {
            $errors[] = $error;
        }
        $user->saveInfo('first_name', $_POST['first_name']);


        // Require Last Name
        if (isset($_POST['sk5tyelo'])) {
            $_POST['last_name'] = $_POST['sk5tyelo'];
        }
        $error = Validate::stringRequired($_POST['last_name']) ? '' : 'Please supply your last name.';
        if (!empty($error)) {
            $errors[] = $error;
        }
        $user->saveInfo('last_name', $_POST['last_name']);

        // Require Valid Email Address
        if (isset($_POST['mi0moecs'])) {
            $_POST['email'] = $_POST['mi0moecs'];
        }
        $error = Validate::email($_POST['email']) ? '' : 'Please supply a valid email address.';
        if (!empty($error)) {
            $errors[] = $error;
        }
        $user->saveInfo('email', $_POST['email']);

        // Check Phone Number
        if (Validate::stringRequired($_POST['phone']) || !empty(Settings::getInstance()->SETTINGS['registration_phone'])) {
            $error = Validate::phone($_POST['phone']) ? '' : 'Please supply a valid phone number.';
            if (!empty($error)) {
                $errors[] = $error;
            }
            $user->saveInfo('phone', $_POST['phone']);
        }

        // Require Compliance Agreement
        if (!empty($_COMPLIANCE['register']['agree'])) {
            $agree = $_COMPLIANCE['register']['agree'];
            if (is_array($agree) && empty($_POST['agree'])) {
                $errors[] = 'You must agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.';
            }
        }

        // Check for valid phone number
        $image_name = '';
        if (!empty($_POST['image']) && !$_POST['image_default']) {
            $image_size = @getimagesize($_POST['image']);
            if (!empty($image_size)) {
                $mime = $image_size['mime'];
                $extensions = ['image/jpeg' => 'jpeg', 'image/gif' => 'gif', 'image/png' => 'png'];
                if (!empty($extensions[$mime])) {
                    $image = file_get_contents($_POST['image']);
                    $image_name = mt_rand() . '.' . $extensions[$mime];
                    $image_location = $_SERVER['DOCUMENT_ROOT'] . '/uploads/leads/' . $image_name;
                    if (!file_put_contents($image_location, $image)) {
                        unset($image_name);
                    }
                }
            }
        }

        // Check For Potentially Malicious Submission Data
        $check_fields = array(
            'onc5khko' => $_POST['onc5khko'],
            'sk5tyelo' => $_POST['sk5tyelo']
        );
        list($not_allowed, $bad_fields) = Validate::formFields($check_fields);
        foreach ($bad_fields as $bad_field) {
            $errors[] = 'We are sorry.  We are unable to process your submission as ' . $bad_field . ' contains at least one of the following characters: ' . implode(', ', Format::htmlspecialchars($not_allowed));
        }

        // Check Duplicate Email
        $duplicate = $db_users->fetchQuery("SELECT COUNT(*) AS `total` FROM `users` WHERE `email` = '" . $db_users->cleanInput($_POST['email']) . "';");
        if (!empty($duplicate['total'])) {
            $errors[] = 'The supplied email address already belongs to a registered user.';
        }

        // No Errors
        if (empty($errors)) {
            try {
                // Get Leads DB
                $db = DB::get('users');

                // Create New Lead
                $lead = new Backend_Lead(array(
                    $oauth_token    => $token,
                    $oauth_profile  => json_encode($profile),
                    'first_name'    => $_POST['first_name'],
                    'last_name'     => $_POST['last_name'],
                    'email'         => $_POST['email'],
                    'phone'         => $_POST['phone'],
                    'opt_marketing' => isset($_POST['opt_marketing']) ? 'in' : 'out',   // Opt-In
                    'verified'      => 'yes',                                           // Auto-Verify
                    'image'         => $image_name
                ));

                // Save Lead
                if ($lead->save($db)) {
                    // Set User Id (Log In)
                    $user->setUserId($lead->getId());

                    // Success
                    $success[] = 'You have successfully been registered!';

                    // Hide Form
                    $show_form = false;

                    // Log Event: Lead Registered via Third-Party
                    $event = new History_Event_Action_Connected(array(
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'name' => $provider,
                        'data' => $profile,
                        'type' => 'register'
                    ), array(
                        new History_User_Lead($lead->getId())
                    ));

                    // Save to DB
                    $event->save($db);

                    // Run hook
                    Hooks::hook(Hooks::HOOK_LEAD_FORM_SUBMISSION)->run($_POST, 'IDX Registration', $lead->getRow());

                    // Get Lead Agent
                    $agent = Backend_Agent::load($lead['agent']);

                    // URL to Lead Details
                    $url_lead = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/edit/?id=' . $lead['id'];

                    // Generate Message
                    $message  = '<p><a href="' . $url_lead . '">' . $lead['first_name'] . ' ' . $lead['last_name'] . '</a> has registered via <strong>' . $provider . '</strong>.</p>';
                    $message .= '<========================>' . '<br>';
                    if (!empty($profile['image'])) {
                        $message .= '<img src="' . $profile['image'] . '" alt=""><br>';
                    }
                    if (!empty($profile['link'])) {
                        $message .= '<strong>Profile:</strong> <a href="' . $profile['link'] . '">' . $profile['link'] . '</a><br>';
                    }
                    $message .= '<strong>Email:</strong> <a href="mailto:' . $lead['email'] . '">' . $lead['email'] . '</a><br>';
                    $message .= '<strong>IP Address:</strong> ' . $_SERVER['REMOTE_ADDR'] . '<br>';
                    $message .= '<========================>';
                    $message .= '<p>The following information was collected about the user:</p>';
                    $message .= $user->formatUserInfo();
                    $message .= '<========================>';
                    $message .= '<p>Have a nice day!</p>';

                    // Notify Agent
                    $mailer = new Backend_Mailer_SMS(array(
                        'subject'       => 'Connected via ' . $provider,
                        'message'       => $message,
                        'sms_message'   => '*IDX Social Connect*'
                            . PHP_EOL . $lead['first_name'] . ' ' . $lead['last_name'] . (!empty($lead['phone']) ? ' ' . $lead['phone'] : '')
                            . PHP_EOL . $url_lead
                    ));

                    // Set Recipient
                    $mailer->setRecipient($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);

                    // Check Incoming Notification Settings
                    $check = $agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_LEAD_INQUIRED);

                    // Send Email
                    if (!empty($check)) {
                        $mailer->Send();
                    }

                    // Send 'Social Connect' Auto-Responder
                    $autoresponder = $db->fetch("SELECT * FROM `auto_responders` WHERE `id` = '12' AND `active` = 'Y';");
                    if (!empty($autoresponder)) {
                        // Setup Mailer
                        $mailer = new Backend_Mailer_FormAutoResponder($autoresponder);

                        // Set Recipient
                        $mailer->setRecipient($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

                        // Send Email
                        $mailer->Send(array(
                            'id'          => $lead['id'],
                            'agent'       => $lead['agent'],
                            'first_name'  => $lead['first_name'],
                            'last_name'   => $lead['last_name'],
                            'email'       => $lead['email']
                        ));
                    }

                    Hooks::hook(Hooks::HOOK_LEAD_CREATED)->run($lead);
                } else {
                    // Error
                    $errors[] = 'An error has occurred. Your registration could not be completed.';
                }

            // Exception Caught
            } catch (Exception $e) {
                $errors[] = 'An error has occurred. Please try again.';
                Log::error($e);
            }
        }
    }

    // Auto-Fill User Data
    $first_name    = isset($_POST['first_name'])    ? $_POST['first_name']      : $profile['first_name'];
    $last_name     = isset($_POST['last_name'])     ? $_POST['last_name']       : $profile['last_name'];
    $email         = isset($_POST['email'])         ? $_POST['email']           : $profile['email'];
    $phone         = isset($_POST['phone'])         ? $_POST['phone']           : '';
    $image         = isset($_POST['image'])         ? $_POST['image']           : $profile['image'];
    $image_default = isset($_POST['image_default']) ? $_POST['image_default']   : $profile['image_default'];
    $opt_marketing = (!empty($anti_spam['optin']) ? $anti_spam['optin'] : $user->info('opt_marketing'));
    $opt_texts = $anti_spam_sms['optin'] ?: $user->info('opt_texts');

// Not Connected
} else {
    // Redirect to Registration Form
    $append = (isset($_GET['popup']) ? '?popup' : '');
    header('Location: ' . Settings::getInstance()->SETTINGS['URL_IDX_REGISTER'] . $append);
    exit;
}
