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

/**
 * Sort Blog Categories
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
            try {
                $db->prepare("UPDATE `" . TABLE_BLOG_CATEGORIES . "` SET `order` = '" . $order . "' WHERE `id` = :id;")->execute(["id" => $category]);
            } catch (PDOException $e) {}

            /* Increment */
            $order++;
        }
    }

    /* Send as JSON */
    header('Content-type: application/json');

    /* Return JSON */
    die(json_encode($json));
}

/**
 * Delete Blog Category
 */
if (!empty($_POST['delete'])) {
    /* Select Row */
    try {
        $row = $db->fetch("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `id` = :id;", ["id" => $_POST['delete']]);
    } catch (PDOException $e) {}

    /* Require Row */
    if (!empty($row)) {
        try {
            /* Build DELETE Query */
            $db->prepare("DELETE FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `id` = :id;")->execute(["id" => $row['id']]);

            /* Success */
            $success[] = __('The selected blog category has successfully been deleted.');

            try {
                /* Delete Sub-Categories */
                $db->prepare("DELETE FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = :parent;")->execute(["parent" => $row['link']]);
            } catch (PDOException $e) {}
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('The selected blog category could not be deleted.');
        }
    } else {
        /* Error */
        $errors[] = __('The selected blog category could not be found.');
    }
}

/* Count Rows */
try {
    $count_categories = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_CATEGORIES . "`;");
} catch (PDOException $e) {}

/* Check Count */
if (!empty($count_categories['total'])) {
    /* Blog Categories */
    $categories = array();

    try {
        /* Build Collection */
        foreach ($db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = '' ORDER BY `order` ASC;") as $manage_category) {
            /* Select Sub Categories */
            $manage_category['subcategories'] = array();
            $subcategories = mysql_query("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = '" . $manage_category['link'] . "' ORDER BY `order` ASC;");
            try {
                $manage_category['subcategories'] = $db->fetchAll("SELECT * FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `parent` = :parent ORDER BY `order` ASC;", ["parent" => $manage_category['link']]);
            } catch (PDOException $e) {}

            /* Add to Collection */
            $categories[] = $manage_category;
        }
    } catch (PDOException $e) {}

}
