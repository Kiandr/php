<?php

// Get Database
$db = DB::get();

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$blogAuth = new REW\Backend\Auth\BlogsAuth(Settings::getInstance());

// Authorized to Edit Blog Categories
if (!$blogAuth->canManageLinks($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to add blog links.')
    );
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Row ID */
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

/* Select Row */
try {
    $edit_link = $db->fetch("SELECT * FROM `" . TABLE_BLOG_LINKS . "` WHERE `id` = :id", ["id" => $_GET['id']]);
} catch (PDOException $e) {}

// Throw Missing Link Exception
if (empty($edit_link)) {
    throw new \REW\Backend\Exceptions\MissingId\Blog\MissingLinkException();
}

/* New Row Successful */
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = __('Blog Link has successfully been created.');
}

/* Submit Form */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'title', 'title' => __('Link Title'));
    $required[] = array('value' => 'link',  'title' => __('Link URL'));

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    /* Check Errors */
    if (empty($errors)) {
        try {
            /* Build UPDATE Query */
            $db->prepare("UPDATE `" . TABLE_BLOG_LINKS . "` SET "
                   . "`title`             = :title, "
                   . "`link`              = :link, "
                   . "`target`            = :target, "
                   . "`timestamp_updated` = NOW()"
                   . " WHERE "
                   . "`id` = :id;")
            ->execute([
                "title" => $_POST['title'],
                "link" => $_POST['link'],
                "target" => $_POST['target'],
                "id" => $edit_link['id']
            ]);

            /* Success */
            $success[] = __('Blog Link has successfully been saved.');

            /* Fetch Updated Row */
            try {
                $edit_link = $db->fetch("SELECT * FROM `" . TABLE_BLOG_LINKS . "` WHERE `id` = :id;", ["id" => $edit_link['id']]);
            } catch (PDOException $e) {}
        } catch (PDOException $e) {
            /* Error */
            $errors[] = __('Error occurred, Blog Link could not be saved.');
        }
    }
}

/* Use $_POST */
foreach ($edit_link as $k => $v) {
    $edit_link[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
}
