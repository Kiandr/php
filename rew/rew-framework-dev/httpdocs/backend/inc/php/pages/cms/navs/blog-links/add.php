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

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Clear Default */
    if ($_POST['link'] == Http_Uri::getScheme() . '://') {
        $_POST['link'] = '';
    }

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
        try{
            /* Build INSERT Query */
            $db->prepare("INSERT INTO `" . TABLE_BLOG_LINKS . "` SET "
                   . "`title`             = :title, "
                   . "`link`              = :link, "
                   . "`target`            = :target, "
                   . "`timestamp_created` = NOW();")
            ->execute([
                "title" => $_POST['title'],
                "link" => $_POST['link'],
                "target" => $_POST['target']
            ]);

            /* Insert ID */
            $insert_id = $db->lastInsertId();

            /* Redirect to Edit Form */
            header('Location: ../edit/?id=' . $insert_id . '&success=add');

            /* Exit Script */
            exit;
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('Error occurred, Blog Link could not be saved.');
        }
    }
}

/* Default $_POST */
$_POST['link'] = !empty($_POST['link']) ? $_POST['link'] : Http_Uri::getScheme() . '://';
