<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$lead = $db->fetch("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = :id;", ['id' => $_GET['id']]);

/* Throw Missing $lead Exception */
if (empty($lead)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingLeadException();
}

// Create lead instance
$lead = new Backend_Lead($lead);

// Get Lead Authorization
$leadAuth = new REW\Backend\Auth\Leads\LeadAuth($settings, $authuser, $lead);

// Not authorized to text lead
if (!$leadAuth->canTextLead()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to text this lead'
    );
}

// Check for lead warnings
if ($lead->info('opt_texts') == 'out') {
    $errors[] = 'This lead has unsubscribed from marketing texts.';
}

// Check if Action Plan Task is Being Processed
$_GET['post_task'] = (!empty($_GET['post_task'])) ? $_GET['post_task'] : $_POST['post_task'];

// Require php-libphonenumber for formatting and validating phone numbers
require_once $settings->DIRS['LIB'] . 'libphonenumber/autoload.php';
$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

try {
    // Send to this number
    $to = isset($lead['phone']) ? $lead['phone'] : $lead['phone_cell'];
    if (isset($_POST['to'])) {
        $to = $_POST['to'];
    }
    if (isset($_GET['to'])) {
        $to = $_GET['to'];
    }

    // Validate phone number
    $phone_check = $lead->validateCellNumber($to);
    $phone_error = false;

// Invalid phone number
} catch (Exception $e) {
    $phone_error = $e->getMessage();
}

try {
    // Available twilio numbers
    $twilio = Partner_Twilio::getInstance();
    $numbers = $twilio->getTwilioNumbers();

    // Send from first available phone number
    $numbers = array_slice($numbers, 0, 1);
    if (!empty($numbers)) {
        $from = $numbers[0]['phone_number'];
    }

// REW Twilio error exception
} catch (Partner_Twilio_Exception $e) {
    $errors[] = $e->getMessage();
}

// Character limit
$maxlength = 160;

// Media attachment
$media = false;

// Handle form submission
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST' && empty($errors)) {
    try {
        // SMS details
        $to = Format::trim($_POST['to']);
        $body = Format::trim($_POST['body']);
        $media = $_POST['media'] ?: null;

        try {
            // Validate cell phone number
            $phone_check = $lead->validateCellNumber($to);
            $phone_error = false;

        // Validation error occurred
        } catch (UnexpectedValueException $e) {
            $phone_error = $e->getMessage();
            throw $e;
        }

        // Media attachment URL
        if (!empty($media)) {
            if (filter_var($media, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === false) {
                unset($media);
                throw new UnexpectedValueException('Invalid media attachment.');
            }
        }

        // If no media, require a message body
        if (empty($media) && empty($body)) {
            throw new UnexpectedValueException('You must provide a message to send.');

        // Check message length
        } else if (strlen($body) > $maxlength) {
            throw new UnexpectedValueException('You cannot send a message longer than ' . $maxlength . ' characters.');
        }

        // Require valid sender
        if (empty($from)) {
            throw new UnexpectedValueException('You must choose a number to send from.');
        }

        // Generate message body
        $replace = array('{first_name}' => $lead['first_name'], '{last_name}' => $lead['last_name']);
        $body = str_replace(array_keys($replace), array_values($replace), $body);

        // Send text message to lead
        $twilio->sendSmsMessage($to, $from, $body, $media);

        // Success
        $success[] = 'Your text message has been sent.';

        // Track outgoing text message
        (new History_Event_Text_Outgoing(array(
            'to'    => $to,
            'from'  => $from,
            'body'  => $body,
            'media' => $media
        ), array(
            new History_User_Lead($lead['id']),
            $authuser->getHistoryUser()
        )))->save();

        // Redirect back to form
        $authuser->setNotices($success, $errors);
        header('Location: ?id=' . $lead['id'] . '&success' . (isset($_GET['popup']) ? '&popup' : ''));
        exit;

    // REW Twilio error exception
    } catch (Partner_Twilio_Exception $e) {
        $errors[] = $e->getMessage();

    // Validation error has occurred
    } catch (UnexpectedValueException $e) {
        $errors[] = $e->getMessage();

    // Unexpected error
    } catch (Exception $e) {
        $errors[] = 'Something went wrong.';
        //$errors[] = $e->getMessage();
    }
} else {
    $authuser->setNotices($success, $errors);
}
