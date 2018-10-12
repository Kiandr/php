<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Documents
if (!$leadsAuth->canManageDocuments($authuser)) {
    $sql_agent = $authuser->info('id');
}

// Success
$success = array();

// Errors
$errors = array();

// Can Share
$can_share = true;

// Preview Only
$preview = false;

// Selected Record
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
if (is_numeric($_GET['id'])) {
    // Fetch Document
    $result = mysql_query("SELECT `d`.*, `c`.`agent_id` FROM `" . LM_TABLE_DOCS . "` `d`"
        . " LEFT JOIN `" . LM_TABLE_DOC_CATEGORIES . "` `c` ON `d`.`cat_id` = `c`.`id` WHERE `d`.`id` = '" . mysql_real_escape_string($_GET['id']) . "'"
        . (!empty($sql_agent) ? " AND `c`.`agent_id` = '" . $sql_agent . "'" : "")
    . ";");
    $document = mysql_fetch_assoc($result);

    // Document not found
    if (empty($document)) {
        $errors[] = __('The selected document could not be found.');
        $authuser->setNotices($success, $errors);
        header('Location: ../?tab=documents');
        exit;
    }

// Preview Document
} elseif (!empty($_GET['view'])) {
    // Fetch Document
    $result = mysql_query("SELECT `d`.*, `c`.`agent_id` FROM `" . LM_TABLE_DOCS . "` `d`"
        . " LEFT JOIN `" . LM_TABLE_DOC_CATEGORIES . "` `c` ON `d`.`cat_id` = `c`.`id` WHERE `d`.`id` = '" . mysql_real_escape_string($_GET['view']) . "'"
        . (!empty($sql_agent) ? " AND (`d`.`share` = 'true' OR `c`.`agent_id` = '" . $sql_agent . "')" : "")
        . ";");
    $document = mysql_fetch_assoc($result);

    // Document not found
    if (empty($document)) {
        $errors[] = __('The selected document could not be found.');
        $authuser->setNotices($success, $errors);
        header('Location: ../?tab=documents');
        exit;
    }

    // Preview Only
    if (isset($_GET['view'])) {
        $preview = true;
    }
} else {
    // HTML by Default
    $document['is_html'] = 'true';
}

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'name',      'title' => __('Document Name'));
    $required[] = array('value' => 'category',  'title' => __('Document Category'));
    $required[] = array('value' => 'document',  'title' => __('Document Text'));
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Shared Document
        $_POST['share'] = (!empty($can_share) && $_POST['share'] === 'true') ? 'true' : 'false';

        // Document Exists, UPDATE Row
        if (is_numeric($document['id'])) {
            // Build UPDATE Query
            $query = "UPDATE `" . LM_TABLE_DOCS . "` SET "
                . "`name`		= '" . mysql_real_escape_string($_POST['name']) . "', "
                . "`cat_id`		= '" . mysql_real_escape_string($_POST['category']) . "', "
                . "`is_html`	= '" . mysql_real_escape_string($_POST['is_html']) . "', "
                . (!empty($can_share) ? "`share` = '" . mysql_real_escape_string($_POST['share']) . "', " : "")
                . "`document`	= '" . mysql_real_escape_string($_POST['document']) . "'"
                . " WHERE `id`	= '" . $document['id'] . "';";

            // Execute Query
            if (mysql_query($query)) {
                $success[] = __('The selected document has successfully been updated.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect Back to Edit Form
                header('Location: ?id=' . $document['id']);
                exit;

            // Query Error
            } else {
                $errors[] = __('An error occurred while attempting to edit the selected document.');
            }
        } else {
            // Build INSERT Query
            $query = "INSERT INTO `" . LM_TABLE_DOCS . "` SET "
                . "`name`		= '" . mysql_real_escape_string($_POST['name']) . "', "
                . "`cat_id`		= '" . mysql_real_escape_string($_POST['category']) . "', "
                . "`is_html`	= '" . mysql_real_escape_string($_POST['is_html']) . "', "
                . "`document`	= '" . mysql_real_escape_string($_POST['document']) . "',"
                . (!empty($can_share) ? "`share` = '" . mysql_real_escape_string($_POST['share']) . "'," : "")
                . "`timestamp`	= NOW();";

            // Execute Query
            if (mysql_query($query)) {
                $success[] = __('Your new document has successfully been created.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                $insert_id = mysql_insert_id();
                header('Location: ?id=' . $insert_id);
                exit;

            // Query Error
            } else {
                $errors[] = __('An error occurred while attempting to create new document.');
            }
        }
    }

    // Use $_POST Data
    foreach ($document as $k => $v) {
        $document[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

// Warning: Plaintext contains HTML
if ($document['is_html'] !== 'true') {
    if (strlen($document['document']) != strlen(strip_tags($document['document']))) {
        $warnings = array(__('Your plain text document contains HTML code. Suggested Action: Switch to WYSIWYG Editor'));
    }
}

// Document Category
$document['category'] = isset($document['cat_id'])  ? $document['cat_id']   : $_GET['category'];
$document['category'] = isset($_POST['category'])   ? $_POST['category']    : $document['category'];
$document['category'] = isset($_GET['category'])    ? $_GET['category']     : $document['category'];

// Document Categories
$categories = array();
if ($result = mysql_query("SELECT `c`.* FROM `" . LM_TABLE_DOC_CATEGORIES . "` `c`"
    . (!empty($sql_agent) ? " WHERE `c`.`agent_id` = '" . $sql_agent . "'" : "")
    . " ORDER BY `c`.`name` ASC"
. ";")) {
    while ($row = mysql_fetch_assoc($result)) {
        $categories[] = $row;
    }
}
