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

/* Row ID */
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

/* Select Row */
try {
    $edit_category = $db->fetch("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `id` = :id;", ["id" => $_GET['id']]);
} catch (PDOException $e) {}

/* Throw Missing ID Exception */
if (empty($edit_category)) {
    throw new \REW\Backend\Exceptions\MissingId\Blog\MissingCategoryException();
}

/* New Row Successful */
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = __('Blog Category has successfully been created.');
}

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
        try {
            /* Build UPDATE Query */
            $db->prepare("UPDATE `" . TABLE_BLOG_CATEGORIES . "` SET "
                   . "`parent`            = :parent, "
                   . "`title`             = :title, "
                   . "`description`       = :description, "
                   . "`page_title`        = :page_title, "
                   . "`meta_tag_desc`     = :meta_tag_desc, "
                   . "`timestamp_updated` = NOW()"
                   . " WHERE "
                   . "`id` = :id;")->execute([
                "parent"                => $_POST["parent"],
                "title"                 => $_POST["title"],
                "description"           => $_POST["description"],
                "page_title"            => $_POST["page_title"],
                "meta_tag_desc"         => $_POST["meta_tag_desc"],
                "id"                    => $edit_category['id']
            ]);

            /* Success */
            $success[] = __('Blog Category has successfully been saved.');

            /* Fetch Updated Row */
            try {
                $edit_category = $db->fetch("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE id = :id;", ["id" => $edit_category['id']]);
            } catch (PDOException $e) {}
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('Error occurred, Blog Category could not be saved.');
        }
    }

    /* Use $_POST */
    foreach ($edit_category as $k => $v) {
        $edit_category[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

/* Blog Categories */
$categories = array();

/* Build Collection */
try {
    foreach ($db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = '' AND `id` != :id ORDER BY `order`;", ["id" => $edit_category['id']]) as $category) {
        /* Add to Collection */
        $categories[] = $category;
    }
} catch (PDOException $e) {}
