<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Documents
if (!$leadsAuth->canManageDocuments($authuser)) {
    $sql_agent = "`agent_id` = '" . $authuser->info('id') . "'";
}

// Success
$success = array();

// Errors
$errors = array();

// Preview Template
if (!empty($_GET['view'])) {
    // Select Record
    $result = mysql_query("SELECT * FROM " . LM_TABLE_DOC_TEMPLATES . " WHERE `id` = '" . mysql_real_escape_string($_GET['view']) . "'" . (!empty($sql_agent) ? " AND (" . $sql_agent . " OR `share` = 'true')" : '') . ";");
    $template = mysql_fetch_assoc($result);
    if (!empty($template)) {
        // Preview Only
        $preview = true;
    }

// Add / Edit Template
} else {
    // Selected Row
    $_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

    if (is_numeric($_GET['id'])) {
        // Fetch Template
        $result = mysql_query("SELECT * FROM " . LM_TABLE_DOC_TEMPLATES . " WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'" . (!empty($sql_agent) ? ' AND ' . $sql_agent : '') . ";");
        $template = mysql_fetch_assoc($result);

        // Template not found
        if (empty($template)) {
            $errors[] = 'The selected template could not be found.';
            $authuser->setNotices($success, $errors);
            header('Location: ../?tab=templates');
            exit;
        }
    } else {
        // New Template
        $template = array('agent_id' => $authuser->info('id'));
    }

    // Can this User Share Templates with Agents?
    $can_share = ($leadsAuth->canManageDocuments($authuser)
        && $template['agent_id'] == 1) ? true : false;

    // Process Submit
    if (isset($_GET['submit'])) {
        // Required Fields
        $required   = array();
        $required[] = array('value' => 'name',  'title' => 'Template Name');
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = $require['title'] . ' is a required field.';
            }
        }

        // Check Errors
        if (empty($errors)) {
            // Template Exists, UPDATE Row
            if (is_numeric($template['id'])) {
                // Share with Agents
                $_POST['share'] = (!empty($can_share) && ($_POST['share'] == 'true')) ? 'true' : 'false';

                // Build UPDATE Query
                $query = "UPDATE `" . LM_TABLE_DOC_TEMPLATES . "` SET "
                    . "`name`		= '" . mysql_real_escape_string($_POST['name']) . "', "
                    . "`template`	= '" . mysql_real_escape_string($_POST['template']) . "', "
                    . "`share`		= '" . mysql_real_escape_string($_POST['share']) . "' "
                    . " WHERE `id`	= '" . $template['id'] . "';";

                // Execute Query
                if (mysql_query($query)) {
                    $success[] = 'The selected template has successfully been updated.';

                    // Save Notices
                    $authuser->setNotices($success, $errors);

                    // Redirect Back to Edit Form
                    header('Location: ?id=' . $template['id']);
                    exit;

                // Query Error
                } else {
                    $errors[] = 'An error occurred while attempting to edit the selected template.';
                }
            } else {
                // Share with Agents
                $_POST['share'] = (!empty($can_share) && ($_POST['share'] == 'true')) ? 'true' : 'false';

                // Build INSERT Query
                $query = "INSERT INTO `" . LM_TABLE_DOC_TEMPLATES . "` SET "
                    . "`agent_id`	= '" . $authuser->info('id') . "', "
                    . "`name`		= '" . mysql_real_escape_string($_POST['name']) . "', "
                    . "`template`	= '" . mysql_real_escape_string($_POST['template']) . "', "
                    . "`share`		= '" . mysql_real_escape_string($_POST['share']) . "', "
                    . "`timestamp`	= NOW();";

                // Execute Query
                if (mysql_query($query)) {
                    $success[] = 'Your new template has successfully been created.';

                    // Save Notices
                    $authuser->setNotices($success, $errors);

                    // Redirect to Edit Form
                    $insert_id = mysql_insert_id();
                    header('Location: ?id=' . $insert_id);
                    exit;

                // Query Error
                } else {
                    $errors[] = 'An error occurred while attempting to create new template.';
                }
            }
        }

        // Use $_POST Data
        foreach ($template as $k => $v) {
            $template[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
        }
    }
}
