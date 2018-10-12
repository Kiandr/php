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

/**
 * Sort Blog Links
 */
if (isset($_GET['order'])) {
    /* JSON */
    $json = array();

    /* Check Offices */
    if (!empty($_POST['links']) && is_array($_POST['links'])) {
        /* Sort Key */
        $order = 0;

        /* Loop through Offices */
        foreach ($_POST['links'] as $link) {
            /* Skip Empty */
            if (empty($link)) {
                continue;
            }

            /* Build DELETE Query */
            try {
                $db->prepare("UPDATE `" . TABLE_BLOG_LINKS . "` SET `order` = '" . $order . "' WHERE `id` = :id;")
                    ->execute([
                        "id" => $link
                    ]);
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
 * Delete Blog Link
 */
if (!empty($_GET['delete'])) {
    /* Locate Row */
    try {
        $row = $db->fetch("SELECT * FROM `" . TABLE_BLOG_LINKS . "` WHERE `id` = :id;", ["id" => $_GET['delete']]);
    } catch (PDOException $e) {}

    /* Require Row */
    if (!empty($row)) {
        try {
            /* Build DELETE Query */
            $db->prepare("DELETE FROM `" . TABLE_BLOG_LINKS . "` WHERE `id` = :id")
            ->execute([
                "id" => $row['id']
            ]);

            /* Success */
            $success[] = __('The selected blog link has successfully been deleted.');

            /* Delete Sub-Links */
            try {
                $db->prepare("DELETE FROM `" . TABLE_BLOG_LINKS . "` WHERE `parent` = :parent;")
                ->execute([
                    "parent" => $row['link']
                ]);
            } catch (PDOException $e) {}
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('The selected blog link could not be deleted.');
        }
    } else {
        /* Error */
        $errors[] = __('The selected blog link could not be found.');
    }
}

/* Count Rows */
try {
    $count_links = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_LINKS . "`;");
} catch (PDOException $e) {}

/* Check Count */
if (!empty($count_links['total'])) {
    /* Blog Links */
    try {
        $links = $db->fetchAll("SELECT * FROM `" . TABLE_BLOG_LINKS . "` ORDER BY `order` ASC;");
    } catch (PDOException $e) {
        $links = [];
    }
}
