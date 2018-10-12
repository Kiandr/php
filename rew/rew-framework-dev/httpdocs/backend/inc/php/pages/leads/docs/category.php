<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage All Documents
if (!$leadsAuth->canManageDocuments($authuser)) {
    $sql_agent = " AND `agent_id` = '" . $authuser->info('id') . "'";
}

// Success
$success = array();

// Errors
$errors = array();

// Selected Row
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
if (is_numeric($_GET['id'])) {
    // Fetch Category
    $result = mysql_query("SELECT * FROM " . LM_TABLE_DOC_CATEGORIES . " WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "'"
    . $sql_agent
    . ";");
    $category = mysql_fetch_assoc($result);

    // Category not found
    if (empty($category)) {
        $errors[] = __('The selected category could not be found.');
        $authuser->setNotices($success, $errors);
        header('Location: ../');
        exit;
    }
}

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'name', 'title' => __('Category Name'));
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Category Exists, UPDATE Row
        if (is_numeric($category['id'])) {
            // Build UPDATE Query
            $query = "UPDATE `" . LM_TABLE_DOC_CATEGORIES . "` SET "
                . "`name`			= '" . mysql_real_escape_string($_POST['name']) . "', "
                . "`description`	= '" . mysql_real_escape_string($_POST['description']) . "'"
                . " WHERE `id`		= '" . $category['id'] . "';";

            if (mysql_query($query)) {
                $success[] = __('The selected category has successfully been updated.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect Back to Edit Form
                header('Location: ?id=' . $category['id']);
                exit;

            // Query Error
            } else {
                $errors[] = __('An error occurred while attempting to edit the selected category.');
            }
        } else {
            // Build INSERT Query
            $query = "INSERT INTO `" . LM_TABLE_DOC_CATEGORIES . "` SET "
                . "`name`			= '" . mysql_real_escape_string($_POST['name']) . "', "
                . "`description`	= '" . mysql_real_escape_string($_POST['description']) . "', "
                . "`agent_id`		= '" . $authuser->info('id') . "';";

            // Execute Query
            if (mysql_query($query)) {
                $success[] = __('Your new document category has successfully been created.');

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                $insert_id = mysql_insert_id();
                header('Location: ?id=' . $insert_id);
                exit;

            // Query Error
            } else {
                $errors[] = __('An error occurred while attempting to create new category.');
            }
        }
    }

    // Use $_POST Data
    foreach ($category as $k => $v) {
        $category[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}
