<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage Lead Keywords
if (!$leadsAuth->canManageAutoresponders($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage autoresponders.')
    );
}
// Success
$success = array();

// Errors
$errors = array();

// Selected ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Select Auto-Responder
$result = mysql_query("SELECT * FROM `auto_responders` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "';");
$autoresponder = mysql_fetch_assoc($result);

/* Throw Missing Agent Exception */
if (empty($autoresponder)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAutoresponderException();
}

// Process Submit
if (isset($_GET['submit'])) {
    // Require Enum
    $_POST['from'] = in_array($_POST['from'], array('admin', 'agent', 'custom')) ? $_POST['from'] : 'custom';

    // Require Sender Information
    if ($_POST['from'] == 'custom') {
        // Require Sender Name
        if (empty($_POST['from_name'])) {
            $errors[] = __('Please supply a Sender Name for the auto responder.');
        }
        // Require Sender Email
        if (!Validate::stringRequired($_POST['from_email'])) {
            $errors[] = __('Please supply a valid Sender Email Address.');
        } elseif (!Validate::email($_POST['from_email'])) {
            $errors[] = __('You supplied an invalid Sender Email Address.');
        }
    }

    // Require Valid CC Email (Optional)
    if (!empty($_POST['cc_email']) && !Validate::email($_POST['cc_email'])) {
        $errors[] = __('Invalid CC Email Address Supplied.');
    }

    // Require Valid BCC Email (Optional)
    if (!empty($_POST['bcc_email']) && !Validate::email($_POST['bcc_email'])) {
        $errors[] = __('Invalid BCC Email Address Supplied.');
    }

    // Require Email Subject
    if (empty($_POST['subject'])) {
        $errors[] = __('Please supply a subject for the auto responder.');
    }

    // Require Email Message
    if (empty($_POST['document'])) {
        $errors[] = __('Please supply a message for the auto responder.');
    }

    // Check Errors
    if (empty($errors)) {
        // Require Enums
        $template = !empty($_POST['tempid'])       ? mysql_real_escape_string($_POST['tempid']) : 'NULL';
        $is_html  = ($_POST['is_html'] == 'false') ? 'false' : 'true';
        $active   = ($_POST['active']  == 'Y')     ? 'Y'     : 'N';

        // Only Update Sender Information If Set to 'custom'
        $_POST['from_name']  = ($_POST['from'] == 'custom') ? $_POST['from_name']  : $autoresponder['from_name'];
        $_POST['from_email'] = ($_POST['from'] == 'custom') ? $_POST['from_email'] : $autoresponder['from_email'];

        // Build UPDATE Query
        $query = "UPDATE `auto_responders` SET "
               . "`from`		= '" . mysql_real_escape_string($_POST['from']) . "', "
               . "`from_name`	= '" . mysql_real_escape_string($_POST['from_name']) . "', "
               . "`from_email`	= '" . mysql_real_escape_string($_POST['from_email']) . "', "
               . "`cc_email`	= '" . mysql_real_escape_string($_POST['cc_email']) . "', "
               . "`bcc_email`	= '" . mysql_real_escape_string($_POST['bcc_email']) . "', "
               . "`subject`		= '" . mysql_real_escape_string($_POST['subject']) . "', "
               . "`is_html`		= '" . $is_html  . "', "
               . "`active`		= '" . $active   . "', "
               . "`tempid`		= "  . $template . ", "
               . "`document`	= '" . mysql_real_escape_string($_POST['document']) . "'"
               . " WHERE "
               . "`id` = '" . $autoresponder['id'] . "';";

        // Execute Query
        if (mysql_query($query)) {
            // Success
            $success[] = __('The selected auto responder has successfully been updated.');

        // Query Error
        } else {
            $errors[] = __('An error occurred while attempting to edit the selected auto-responder.');
        }
    }

    // Use $_POST
    foreach ($autoresponder as $k => $v) {
        $autoresponder[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Document Templates
$templates = array();
$result = mysql_query("SELECT `id`, `name` FROM `" . LM_TABLE_DOC_TEMPLATES . "` WHERE `agent_id` = 1 ORDER BY `name` ASC;");
while ($row = mysql_fetch_assoc($result)) {
    $templates[] = $row;
}

// Super Admin Details
$result = mysql_query("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
$super_admin = mysql_fetch_assoc($result);
