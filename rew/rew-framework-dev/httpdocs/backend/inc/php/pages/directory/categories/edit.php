<?php

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$directoryAuth = new REW\Backend\Auth\DirectoryAuth(Settings::getInstance());

// Authorized to manage directories
if (!$directoryAuth->canManageCategories($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to manage categories'
    );
}

/* Authorized to Delete? */
$can_delete = $directoryAuth->canDeleteCategories($authuser);

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Row ID */
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

/* Select Row */
$result = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "';");
$edit_category = mysql_fetch_array($result);

/* Throw Missing ID Exception */
if (empty($edit_category)) {
    throw new \REW\Backend\Exceptions\MissingId\Directory\MissingCategoryException();
}

/* New Row Successful */
if (!empty($_GET['success']) && $_GET['success'] == 'add') {
    $success[] = 'Directory Category has successfully been created.';
}

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'title', 'title' => 'Category Title');

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Require String */
        $_POST['parent'] = is_array($_POST['parent']) ? implode(',', $_POST['parent']) : $_POST['parent'];

        /* Require String */
        $_POST['related_categories'] = is_array($_POST['related_categories']) ? implode(',', $_POST['related_categories']) : $_POST['related_categories'];

        /* Build UPDATE Query */
        $query = "UPDATE `" . TABLE_DIRECTORY_CATEGORIES . "` SET "
               . "`title`              = '" . mysql_real_escape_string($_POST['title']) . "', "
               . "`category_content`   = '" . mysql_real_escape_string($_POST['category_content']) . "', "
               . "`parent`             = '" . mysql_real_escape_string($_POST['parent']) . "', "
               . "`related_categories` = '" . mysql_real_escape_string($_POST['related_categories']) . "', "
               . "`page_title`         = '" . mysql_real_escape_string($_POST['page_title']) . "', "
               . "`meta_tag_keywords`  = '" . mysql_real_escape_string($_POST['meta_tag_keywords']) . "', "
               . "`meta_tag_desc`      = '" . mysql_real_escape_string($_POST['meta_tag_desc']) . "'"
               . " WHERE  "
               . "`id` = '" . $edit_category['id'] . "';";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Sucess */
            $success[] = 'Directory Category has successfully been saved.';

            /* Fetch Updated Row */
            $result = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `id` = '" . $edit_category['id'] . "';");
            $edit_category = mysql_fetch_array($result);
        } else {
            /* Query Error */
            $errors[] = 'Directory Category could not be saved, please try again.';
        }
    }

    /* Use $_POST */
    foreach ($edit_category as $k => $v) {
        $edit_category[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}

/* Require Array */
$edit_category['related_categories'] = is_array($edit_category['related_categories']) ? $edit_category['related_categories'] : explode(',', $edit_category['related_categories']);

/* Categories */
$categories = array();

/* Related Categories */
$related_categories = array();

/* Select Rows */
$directory_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order` ASC, `title` ASC;");

/* Build Collections */
while ($directory_category = mysql_fetch_array($directory_categories)) {
    /* Add to Collection */
    $categories[] = $directory_category;

    /* Add to Collcetion */
    $related_categories[] = $directory_category;

    /* Select Rows */
    $sub_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $directory_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

    /* Build Collection */
    while ($sub_category = mysql_fetch_array($sub_categories)) {
        /* Add to Collection */
        $categories[] = $sub_category;

        /* Selected Sub-Category */
        /* Add to Collection */
        $related_categories[] = $sub_category;

        /* Select Rows */
        $tert_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $sub_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

        /* Build Collection */
        while ($tert_category = mysql_fetch_array($tert_categories)) {
            /* Add to Collection */
            $related_categories[] = $tert_category;
        }
    }
}
