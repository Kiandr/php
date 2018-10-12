<?php

// Get Database
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
$agent = Backend_Agent::load($_GET['id']);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to manage agent's autoresponders
if (!$agentAuth->canManageAgent() && !$agentAuth->isSelf()) {
    if (!$agentAuth->canSetAutoresponders()) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit this agents autoresponders')
        );
    }
}

/**
 * Reset Auto-Responder
 */
if (isset($_GET['reset'])) {
    try {
        // Build UPDATE Query
        $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET "
               . "`ar_subject`   = '', "
               . "`ar_is_html`   = '', "
               . "`ar_active`    = '', "
               . "`ar_tempid`    = NULL, "
               . "`ar_document`  = '', "
               . "`ar_cc_email`  = '', "
               . "`ar_bcc_email` = ''"
               . " WHERE "
               . "`id` = :id;")
        ->execute([
            "id" => $agent['id']
        ]);

        // Success
        $success[] = __('The auto responder has successfully been deleted.');
    } catch (PDOException $e) {
        // Query Error
        $errors[] = __('An error occurred while attempting to deleted the auto responder.');
    }

    // Reset Fields
    $agent['ar_subject']   = '';
    $agent['ar_is_html']   = '';
    $agent['ar_document']  = '';
    $agent['ar_tempid']    = '';
    $agent['ar_active']    = 'N';
    $agent['ar_cc_email']  = '';
    $agent['ar_bcc_email'] = '';
}

/**
 * Save Changes
 */
if (isset($_GET['submit'])) {
    // Require Email Subjcet
    if (empty($_POST['ar_subject'])) {
        $errors[] = __('Please supply a subject for the auto responder.');
    }

    // Require Email Message
    if (empty($_POST['ar_document'])) {
        $errors[] = __('Please supply a message for the auto responder.');
    }

    // Require Valid CC Email
    if (!empty($_POST['ar_cc_email']) && !Validate::email($_POST['ar_cc_email'])) {
        $errors[] = __('Invalid CC Email Address Supplied.');
    }

    // Require Valid BCC Email
    if (!empty($_POST['ar_bcc_email']) && !Validate::email($_POST['ar_bcc_email'])) {
        $errors[] = __('Invalid BCC Email Address Supplied.');
    }

    // Set if Empty
    if (empty($_POST['ar_active'])) {
        $_POST['ar_active'] = 'N';
    }

    // Check Errors
    if (empty($errors)) {
        // Require ENUM
        $template = (!empty($_POST['ar_tempid'])) ? $_POST['ar_tempid'] : null;
        try {
            // Build UPDATE Query
            $db->prepare("UPDATE `" . LM_TABLE_AGENTS . "` SET "
                   . "`ar_subject`   = :ar_subject, "
                   . "`ar_is_html`   = :ar_is_html, "
                   . "`ar_active`    = :ar_active, "
                   . "`ar_tempid`    = :ar_tempid, "
                   . "`ar_cc_email`  = :ar_cc_email, "
                   . "`ar_bcc_email` = :ar_bcc_email, "
                   . "`ar_document`  = :ar_document "
                   . " WHERE "
                   . "`id` = :id;")
            ->execute([
                "ar_subject" => $_POST['ar_subject'],
                "ar_is_html" => $_POST['ar_is_html'],
                "ar_active" => $_POST['ar_active'],
                "ar_tempid" => $template,
                "ar_cc_email" => $_POST['ar_cc_email'],
                "ar_bcc_email" => $_POST['ar_bcc_email'],
                "ar_document" => $_POST['ar_document'],
                "id" =>  $agent['id']
            ]);

            // Success
            $success[] = __('The auto responder has successfully been updated.');
        } catch (PDOException $e) {
            // Query Error
            $errors[] = __('An error occurred while attempting to edit the auto responder.');
        }
    }

    /* Use $_POST */
    $agent['ar_subject']   = $_POST['ar_subject'];
    $agent['ar_document']  = $_POST['ar_document'];
    $agent['ar_is_html']   = $_POST['ar_is_html'];
    $agent['ar_cc_email']  = $_POST['ar_cc_email'];
    $agent['ar_bcc_email'] = $_POST['ar_bcc_email'];
    $agent['ar_tempid']    = $_POST['ar_tempid'];
    $agent['ar_active']    = $_POST['ar_active'];
}

$templates = array();
// Templates
try {
    // Select Rows
    $templates = $db->fetchAll("SELECT `id`, `name` FROM `" . LM_TABLE_DOC_TEMPLATES . "`"
                                   . " WHERE (`agent_id` = :id OR `share` = 'true')"
                                   . " ORDER BY `name` ASC;", ["id" => $agent['id']]);
} catch (PDOException $e) {
    $errors[] = __('Error Occurred while loading Templates.');
}
