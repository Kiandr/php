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

/**
 * Sort Categories
 */
if (isset($_GET['order'])) {
    /* JSON */
    $json = array();

    /* Check Offices */
    if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
        /* Sort Key */
        $order = 0;

        /* Loop through Offices */
        foreach ($_POST['categories'] as $category) {
            /* Skip Empty */
            if (empty($category)) {
                continue;
            }

            /* Build DELETE Query */
            $query = "UPDATE `" . TABLE_DIRECTORY_CATEGORIES . "` SET `order` = '" . $order . "' WHERE `id` = '" . mysql_real_escape_string($category) . "';";

            /* Execute Query */
            mysql_query($query);

            /* Increment */
            $order++;
        }
    }

    /* Send as JSON */
    header('Content-type: application/json');

    /* Return JSON */
    die(json_encode($json));
}

// Delete Category
if (!empty($_GET['delete'])) {
    // Check Permission
    if (empty($can_delete)) {
        $errors[] = 'You do not have the proper permissions to perform this action.';
    } else {
        // Load Category Record
        $query = "SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `id` = '" . mysql_real_escape_string($_GET['delete']) . "';";
        if ($result = mysql_query($query)) {
            $category = mysql_fetch_assoc($result);
            if (empty($category)) {
                $errors[] = 'The selected directory category could not be found.';
            } else {
                // Delete Category
                $query = "DELETE FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `id` = '" . mysql_real_escape_string($category['id']) . "';";
                if (mysql_query($query)) {
                    $success[] = 'The selected directory category has successfully been deleted.';

                    // Delete Children
                    $query = "SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . mysql_real_escape_string($category['link']) . "';";
                    if ($children = mysql_query($query)) {
                        while ($child = mysql_fetch_assoc($children)) {
                            $query = "DELETE FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `id` = '" . mysql_real_escape_string($child['id']) . "';";
                            mysql_query($query);
                            // Delete Sub-Childrem
                            $query = "SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . mysql_real_escape_string($child['link']) . "';";
                            if ($subchildren = mysql_query($query)) {
                                while ($subchild = mysql_fetch_assoc($subchildren)) {
                                    $query = "DELETE FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `id` = '" . mysql_real_escape_string($subchild['id']) . "';";
                                    mysql_query($query);
                                }
                            }
                        }
                    }

                // Query Error
                } else {
                    $errors[] = 'The selected directory category could not be deleted. Please try again.';
                }
            }
        } else {
            $errors[] = 'An error occurred while searching for the selected directory category.';
        }
    }
}

/* Categories */
$manage_categories = array();

/* Count Rows */
$result = mysql_query("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_DIRECTORY_CATEGORIES . "`;");
$count_categories = mysql_fetch_array($result);

/* Select Rows */
$directory_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order` ASC, `title` ASC;");

/* Build Collection */
while ($directory_category = mysql_fetch_array($directory_categories)) {
    /* Sub Categories */
    $directory_category['sub_cats'] = array();

    /* Select Rows */
    $sub_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $directory_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

    /* Build Collection */
    while ($sub_category = mysql_fetch_array($sub_categories)) {
        /* Tert Categories */
        $sub_category['tert_cats'] = array();

        /* Select Rows */
        $tert_categories = mysql_query("SELECT * FROM `" . TABLE_DIRECTORY_CATEGORIES . "` WHERE `parent` = '" . $sub_category['link'] . "' ORDER BY `order` ASC, `title` ASC;");

        /* Build Collection */
        while ($tert_category = mysql_fetch_array($tert_categories)) {
            /* Add to Collection */
            $sub_category['tert_cats'][] = $tert_category;
        }

        /* Add to Collection */
        $directory_category['sub_cats'][] = $sub_category;
    }

    /* Add to Collection */
    $manage_categories[] = $directory_category;
}
