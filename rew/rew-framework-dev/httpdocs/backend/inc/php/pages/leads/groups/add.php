<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Campaigns
if (!$leadsAuth->canManageGroups($authuser)) {
    // Authorized to Manage Own Campaigns
    if (!$leadsAuth->canManageOwnGroups($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage groups.')
        );
    }
}

// Success
$success = array();

// Errors
$errors = array();

// Label Colours Dependency
$di = Container::getInstance();
$labelColours = $di->get(REW\Backend\Store\LabelColourStore::class);
$groupLabels = $labelColours->getLabelColours();

// Can Share Group
$can_share = $leadsAuth->canManageGroups($authuser);

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'name', 'title' => __('Group Name'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Global Group
    $global = (!empty($can_share) && $_POST['global'] == 'true');

    // Check Errors
    if (empty($errors)) {
        // Build INSERT Query
        $query = "INSERT INTO `" . LM_TABLE_GROUPS . "` SET "
               . "`agent_id`	= " . (empty($global) && $authuser->isAgent()       ?   "'" . $authuser->info('id') . "'"   :   "NULL") . ", "
               . "`associate`	= " . (empty($global) && $authuser->isAssociate()   ?   "'" . $authuser->info('id') . "'"   :   "NULL") . ", "
               . "`name`        = '" . mysql_escape_string($_POST['name']) . "', "
               . "`description` = '" . mysql_escape_string($_POST['description']) . "', "
               . "`style`       = '" . mysql_escape_string($_POST['style']) . "', "
               . "`timestamp`   = NOW();";

        // Execute Query
        if (mysql_query($query)) {
            // Insert ID
            $insert_id = mysql_insert_id();

            // Success message
            $success[] = __('Group has successfully been created.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect to Edit Form
            header('Location: ../edit/?id=' . $insert_id);
            exit;

        // Query Error
        } else {
            $errors[] = __('Error occurred, Group could not be added. Please try again.');
        }
    }
}
