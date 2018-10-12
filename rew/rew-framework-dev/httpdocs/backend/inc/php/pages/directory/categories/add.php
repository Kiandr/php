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

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

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

    /* Check Duplicate Category Title */
    $result = mysql_query("SELECT `title` FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `title` = '" . mysql_real_escape_string($_POST['title']) . "';");
    $duplicate = mysql_fetch_array($result);
    if (!empty($duplicate)) {
        $errors[] = 'A category with this title already exists.';
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Create Link */
        $_POST['link'] = Format::slugify($_POST['title']);

        /* Require String */
        $_POST['parent'] = is_array($_POST['parent']) ? implode(',', $_POST['parent']) : $_POST['parent'];

        /* Require String */
        $_POST['related_categories'] = is_array($_POST['related_categories']) ? implode(',', $_POST['related_categories']) : $_POST['related_categories'];

        /* Build INSERT Query */
        $query = "INSERT INTO `" . TABLE_DIRECTORY_CATEGORIES . "` SET "
               . "`link`               = '" . mysql_real_escape_string($_POST['link']) . "', "
               . "`title`              = '" . mysql_real_escape_string($_POST['title']) . "', "
               . "`category_content`   = '" . mysql_real_escape_string($_POST['category_content']) . "', "
               . "`parent`             = '" . mysql_real_escape_string($_POST['parent']) . "', "
               . "`related_categories` = '" . mysql_real_escape_string($_POST['related_categories']) . "', "
               . "`page_title`         = '" . mysql_real_escape_string($_POST['page_title']) . "', "
               . "`meta_tag_keywords`  = '" . mysql_real_escape_string($_POST['meta_tag_keywords']) . "', "
               . "`meta_tag_desc`      = '" . mysql_real_escape_string($_POST['meta_tag_desc']) . "', "
               . "`timestamp_created`  = NOW();";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Insert ID */
            $insert_id = mysql_insert_id();

            /* Redirect to Edit Form */
            header('Location: ../edit/?id=' . $insert_id . '&success=add');

            /* Exit Script */
            exit;
        } else {
            /* Query Error */
            $errors[] = 'Error occurred, Directory Category could not be saved.';
        }
    }
}

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
