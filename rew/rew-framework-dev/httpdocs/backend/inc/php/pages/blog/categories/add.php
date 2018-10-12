<?php

// App DB
$db = DB::get();

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$blogAuth = new REW\Backend\Auth\BlogsAuth(Settings::getInstance());

// Authorized to Edit Blog Categories
if (!$blogAuth->canManageCategories($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to add blog categories.')
    );
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'title', 'title' => __('Title'));

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Create Link */
        $_POST['link'] = Format::slugify($_POST['title']);

        /* Make Sure Link is Unique */
        $_POST['link'] = uniqueLink($_POST['link'], TABLE_BLOG_CATEGORIES, 'link');

        try {
            /* Build INSERT Query */
            $db->prepare("INSERT INTO `" . TABLE_BLOG_CATEGORIES . "` SET "
                   . "`parent`            = :parent, "
                   . "`link`              = :link, "
                   . "`title`             = :title, "
                   . "`description`       = :description, "
                   . "`page_title`        = :page_title, "
                   . "`meta_tag_desc`     = :meta_tag_desc, "
                   . "`timestamp_created` = NOW();")->execute([
                "parent"                => $_POST['parent'],
                "link"                  => $_POST['link'],
                "title"                 => $_POST['title'],
                "description"           => $_POST['description'],
                "page_title"            => $_POST['page_title'],
                "meta_tag_desc"         => $_POST['meta_tag_desc']
            ]);

            /* Insert ID */
            $insert_id = $db->lastInsertId();

            /* Redirect to Edit Form */
            header('Location: ../edit/?id=' . $insert_id . '&success=add');

            /* Exit Script */
            exit;
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('Error occurred, Blog Category could not be saved.');
        }
    }
}

/* Blog Categories */
$categories = array();

/* Select Rows */
try {
    $categories = $db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order`;");
} catch (PDOException $e) {}
