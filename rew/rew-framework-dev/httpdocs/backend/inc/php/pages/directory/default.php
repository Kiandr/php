<?php

/* Full Page */
$body_class = 'full';

// Get Authorization Managers
$directoryAuth = new REW\Backend\Auth\DirectoryAuth(Settings::getInstance());

// Authorized to manage directories
if (!$directoryAuth->canManageDirectories($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to manage directories'
    );
}

/* Success Collection */
$success = array();

/* Error Collection */
$errors = array();

/* Select Row */
$result = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_SETTINGS . "`;");
$directory_settings = mysql_fetch_array($result);

/* Require Row */
if (!empty($directory_settings)) {
    throw new \REW\Backend\Exceptions\MissingSettings\MissingDirectoryException();
}

/* Process Submit */
if (isset($_GET['submit'])) {
    /* Required Fields */
    $required   = array();
    $required[] = array('value' => 'directory_name', 'title' => 'Directory Name');

    /* Process Required Fields */
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = $require['title'] . ' is a required field.';
        }
    }

    /* Check Errors */
    if (empty($errors)) {
        /* Check Input */
        $_POST['hide_slideshow'] = ($_POST['hide_slideshow'] == 't') ? 't' : 'f';
        $_POST['sitemap'] = ($_POST['sitemap'] == 'list') ? 'list' : 'cat';

        /* Extra MySQL */
        $sql_extra = '';

        /* Build UPDATE Query */
        $query = "UPDATE `" . TABLE_DIRECTORY_SETTINGS . "` SET "
                . "`directory_name`    = '" . mysql_real_escape_string($_POST['directory_name']) . "', "
                . "`page_title`        = '" . mysql_real_escape_string($_POST['page_title']) . "', "
                . "`meta_tag_keywords` = '" . mysql_real_escape_string($_POST['meta_tag_keywords']) . "', "
                . "`meta_tag_desc`     = '" . mysql_real_escape_string($_POST['meta_tag_desc']) . "', "
                . $sql_extra
                . "`hide_slideshow`    = '" . mysql_real_escape_string($_POST['hide_slideshow']) . "', "
                . "`sitemap`           = '" . mysql_real_escape_string($_POST['sitemap']) . "', "
                . "`timestamp_updated` = NOW();";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Success */
            $success[] = 'Directory Settings have successfully been saved.';

            /* Fetch Updated Row */
            $result = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_SETTINGS . "`;");
            $directory_settings = mysql_fetch_array($result);
        } else {
            /* Query Error */
            $errors[] = 'Directory Settings could not be saved, please try again.';
        }
    }

    /* Use $_POST */
    foreach ($directory_settings as $k => $v) {
        $directory_settings[$k] = isset($_POST[$k]) ? $_POST[$k] : $v;
    }
}
