<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Success
$success = array();

// Errors
$errors = array();

// Label Colours Dependency
$di = Container::getInstance();
$labelColours = $di->get(REW\Backend\Store\LabelColourStore::class);
$groupLabels = $labelColours->getLabelColours();

// Group ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Locate Group
$result = mysql_query(
    "SELECT * FROM `" . LM_TABLE_GROUPS . "`"
    ." WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "';"
);
$group  = mysql_fetch_assoc($result);


// Authorized to Manage All Groups
if (!$leadsAuth->canManageGroups($authuser)) {
    // Authorized to Manage Own Groups
    if (!$leadsAuth->canManageOwnGroups($authuser)
        || ($authuser->isAgent() && !empty($group['agent_id']) && $authuser->info('id') != $group['agent_id'])
        || ($authuser->isAssociate() && !empty($group['associate']) && $authuser->info('id') != $group['associate'])) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to manage groups.')
        );
    }
}

/* Throw Missing Agent Exception */
if (empty($group)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingGroupException();
}

// Can Share Group
$can_share = ($leadsAuth->canManageGroups($authuser) && ($group['agent_id'] == 1 || (empty($group['agent_id']) && empty($group['associate']))));

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

    // set the agent id to us if its null, then set the agent id for the group to null if its global
    if (!empty($can_share)) {
        $group['agent_id'] = ($group['agent_id'] == null) ? $authuser->info('id') : $group['agent_id'];
        $group['agent_id'] = ($_POST['global'] == 'true') ? null : $group['agent_id'];
    }

    // Check Errors
    if (empty($errors)) {
        // Build UPDATE Query
        $query = "UPDATE `" . LM_TABLE_GROUPS . "` SET "
               . "`agent_id`	= " . (is_null($group['agent_id']) ? "NULL" : "'" . $group['agent_id'] . "'") . ", "
               . "`name`        = '" . mysql_real_escape_string($_POST['name']) . "', "
               . "`description` = '" . mysql_real_escape_string($_POST['description']) . "',"
               . "`style`       = '" . mysql_real_escape_string($_POST['style']) . "'"
               . " WHERE "
               . "`id` = '" . $group['id'] . "';";

        // Execute Query
        if (mysql_query($query)) {
            // Success
            $success[] = __('Group details have been successfully updated.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?id=' . $group['id']);
            exit;

        // Query Error
        } else {
            $errors[] = __('Error occurred, Group could not be updated.');
        }
    }

    // Use $_POST Data
    foreach ($group as $k => $v) {
        $group[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}
