<?php

// Get Authorization Managers
$idxAuth = new REW\Backend\Auth\IDXAuth(Settings::getInstance());

// Authorized to manage directories
if (!$idxAuth->canManageSearch($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage idx searches')
    );
}

// Success
$success = array();

// Errors
$errors = array();

// Row ID
$_GET['search_id'] = isset($_POST['search_id']) ? $_POST['search_id'] : $_GET['search_id'];

// Select Row
$result = mysql_query("SELECT * FROM `" . TABLE_IDX_SEARCHES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['search_id']) . "';");
$search = mysql_fetch_assoc($result);

// Throw Missing Page Exception
if (empty($search)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingSearchException();
}


// Select IDX
if (!empty($search['idx'])) {
    Util_IDX::switchFeed($search['idx']);
}

// IDX objects
$idx = Util_IDX::getIdx();
$db_idx = Util_IDX::getDatabase();

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'title', 'title' => __('Search Title'));

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] =  __('%s is a required field.', $require['title']);
        }
    }

    // Page Limit Between 1 and 48
    $page_limit = $_POST['page_limit'];
    if ($page_limit < 1 || $page_limit > 48) {
        $errors[] = __('Page Limit must be a number between 1 and 48.');
    }

    // Check Errors
    if (empty($errors)) {
        // Generate Criteria
        $criteria = serialize($_POST);

        // Build UPDATE Query
        $query = "UPDATE `" . TABLE_IDX_SEARCHES . "` SET "
            . "`title`             = '" . mysql_real_escape_string($_POST['title']) . "',"
            . "`view`              = '" . mysql_real_escape_string($_POST['view']) . "',"
            . "`sort_by`           = '" . mysql_real_escape_string($_POST['sort_by']) . "',"
            . "`page_limit`        = '" . mysql_real_escape_string($_POST['page_limit']) . "',"
            . "`split`             = '" . mysql_real_escape_string($_POST['split']) . "',"
            . "`panels`            = '" . mysql_real_escape_string(serialize($_POST['panels'])) . "',"
            . "`criteria`          = '" . mysql_real_escape_string($criteria) . "',"
            . "`idx`               = '" . mysql_real_escape_string($_POST['feed']) . "',"
            . "`timestamp_updated` = NOW()"
            . " WHERE "
            . "`id` = '" . $search['id'] . "';";

        // Execute Query
        if (mysql_query($query)) {
            // Success
            $success[] = __('IDX Search has successfully been updated.');

            // Save Notices
            $authuser->setNotices($success, $errors);

            // Redirect Back to Form
            header('Location: ?search_id=' . $search['id'] . '&success');
            exit;
        } else {
            // Query Error
            $errors[] = __('IDX Search could not be updated, please try again.');
        }
    }
}

// Default Criteria
if (!empty($search['criteria'])) {
    $criteria = unserialize($search['criteria']);
}

// Default Split (LEC-2013 Only)
$search['split'] = Skin::getDirectory() === 'lec-2013' ? (!empty($search['split']) ? $search['split'] : -1) : null;

// Default View
$search['view'] = isset($search['view']) ? $search['view'] : 'grid';

// Set $_REQUEST Criteria
$criteria = is_array($criteria) ? $criteria : array();
$_REQUEST = search_criteria($idx, $criteria);

// Saved Panels
$search['panels'] = unserialize($search['panels']);

// Default Panels
$search['panels'] = is_array($search['panels']) && !empty($search['panels']) ? $search['panels'] : [];

// IDX Builder
$builder = new IDX_Builder(array(
    'map' => true,
    'panels' => $search['panels'],
    'split' => $search['split']
));
