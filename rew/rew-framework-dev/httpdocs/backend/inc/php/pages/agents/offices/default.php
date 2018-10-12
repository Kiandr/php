<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization Managers
$agentsAuth = new REW\Backend\Auth\AgentsAuth(Settings::getInstance());

// Authorized to Manage Lead Networks
if (!$agentsAuth->canManageOffices($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to edit agent offices.')
    );
}

    /* Success Collection */
    $success = array();

    /* Error Collection */
    $errors = array();

    /* Update Order */
if (isset($_GET['move'])) {
    /* JSON */
    $json = array();

    /* Check Offices */
    if (!empty($_POST['offices'])) {
        /* Sort Key */
        $sort = 0;

        /* Loop through Offices */
        foreach ($_POST['offices'] as $office) {
            /* Skip Empty */
            if (empty($office)) {
                continue;
            }

            try {
                /* Build UPDATE Query */
                $db->prepare("UPDATE `" . TABLE_FEATURED_OFFICES . "` SET `sort` = '" . $sort . "' WHERE `id` = :id;")->execute(['id' => $office]);
            } catch (PDOException $e) {}

            /* Increment */
            $sort++;
        }
    }

    /* Send as JSON */
    header('Content-type: application/json');

    /* Return JSON */
    die(json_encode($json));
}


    /**
     * Delete Row
     */
if (!empty($_POST['delete'])) {
    /* Select Row */
    try {
        $delete = $db->fetch("SELECT * FROM `" . TABLE_FEATURED_OFFICES . "` WHERE `id` = :id;", ['id' => $_POST['delete']]);
    } catch (PDOException $e) {}

    /* Require Row */
    if (!empty($delete)) {
        try {
            /* Build DELETE Query */
            $query = $db->prepare("DELETE FROM `" . TABLE_FEATURED_OFFICES . "` WHERE `id` = :id;")->execute(['id' => $_POST['delete']]);

            /* Success */
            $success[] = __('The selected featured office has successfully been deleted.');

            /* Delete Photos */
            if (!empty($delete['images'])) {
                foreach (explode(",", $delete['images']) as $image) {
                    @unlink(DIR_FEATURED_IMAGES . $image);
                }
            }
        } catch (PDOException $e) {
            /* Query Error */
            $errors[] = __('The selected featured office could not be deleted. Please try again.');
        }
    } else {
        /* Row not Found */
        $errors[] = __('The selected featured office could not be found.');
    }
}

    /* Offices */
    $manage_offices = array();

    /* Count Rows */
    try {
        $count_featured = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_FEATURED_OFFICES . "`;");
    } catch (PDOException $e) {}

    /* Check Count */
if (!empty($count_featured['total'])) {
    /* Select Rows */
    try {
        foreach($db->fetchAll("SELECT * FROM `" . TABLE_FEATURED_OFFICES . "` ORDER BY `sort` ASC;") as $featured_office) {
            /* Office Agents */
            try {
                $agents = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . LM_TABLE_AGENTS . "` WHERE `office` = :office;", ['office' => $featured_office['id']]);
            } catch (PDOException $e) {}

            /* Agent Count */
            $featured_office['agents'] = $agents['total'];

            /* Add to Collection */
            $manage_offices[] = $featured_office;
        }
    } catch (PDOException $e) {
        /* Query Error */
        $errors[] = __('Error Occurred while loading Offices.');
    }
}
