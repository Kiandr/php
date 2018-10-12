<?php

// App DB
$db = DB::get();

// Full Page
$body_class = 'full';

// Get Authorization Managers
$blogAuth = new REW\Backend\Auth\BlogsAuth(Settings::getInstance());

// Require permission to edit all associates
if (!$blogAuth->canManagePings($authuser)) {
    // Require permission to edit self
    if (!$blogAuth->canManageSelf($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit blog pingbacks.')
        );
    } else {
        // Agent Mode: Only show Agent's Data
        $sql_agent = "`agent` = :agent";
        $sql_agent_id = $authuser->info('id');
    }
// Filter By Agent
} else if (!empty($_GET['filter'])) {
    // Set Agent Filter
    $filterAgent = Backend_Agent::load($_GET['filter']);
    if (isset($filterAgent) && $filterAgent instanceof Backend_Agent) {
        $sql_agent = "`agent` = :agent";
        $sql_agent_id = $filterAgent->getId();
    }
}

// Success
$success = array();

// Error
$errors = array();

// Delete Blog Pingback
if (!empty($_GET['delete'])) {
    try {
        $params = ["id" => $_GET['delete']];
        if(!empty($sql_agent)) {
            $params["agent"] = $sql_agent_id;
        }
        // Delete from Database
        $db->prepare("DELETE FROM `" . TABLE_BLOG_PINGS . "` WHERE `id` = :id $sql_agent;")->execute($params);

        // Success
        $success[] = __('The selected blog ping has successfully been deleted.');

    // Query Error
    } catch (PDOException $e) {
        $errors[] = __('The selected blog ping could not be deleted.');
    }
}

// Group Action
if (!empty($_POST['pings']) && is_array(($_POST['pings']))) {
    // Delete Pingbacks
    $deleted = 0;
    if ($_POST['action'] === 'delete') {
        foreach ($_POST['pings'] as $ping) {
            if (is_numeric($ping)) {
                try {
                    $params = ["id" => $ping];
                    if(!empty($sql_agent)) {
                        $params["agent"] = $sql_agent_id;
                    }
                    $db->prepare("DELETE FROM `" . TABLE_BLOG_PINGS . "` WHERE `id` = :id $sql_agent;")->execute($params);
                    $deleted++;
                } catch (PDOException $e) {}
            }
        }
    }

    // Publish Pingbacks
    $published = 0;
    if ($_POST['action'] === 'publish') {
        foreach ($_POST['pings'] as $ping) {
            if (is_numeric($ping)) {
                try {
                    $params = ["id" => $ping];
                    if(!empty($sql_agent)) {
                        $params["agent"] = $sql_agent_id;
                    }
                    $db->prepare("UPDATE `" . TABLE_BLOG_PINGS . "` SET `published` = 'true' WHERE `id` = :id $sql_agent;")->execute($params);
                    $published++;
                } catch (PDOException $e) {}
            }
        }
    }

    // Pingbacks Deleted
    if (!empty($deleted)) {
        $success[] = __('%s blog %s successfully been deleted.', Format::number($deleted), Format::plural($deleted, 'pingbacks have', 'pingback has'));
    }

    // Pingbacks Published
    if (!empty($published)) {
        $success[] = __('%s blog %s successfully been published.', Format::number($published), Format::plural($published, 'pingbacks have', 'pingback has'));
    }

    // Save Notices
    $authuser->setNotices($success, $errors);

    // Redirect to Manage List
    header('Location: ?success' . (isset($_POST['filter']) ? '&filter=' . $_POST['filter'] : ''));
    exit;
}

// Publish Blog Ping
if (!empty($_GET['publish'])) {
    // Require Pingback
    try {
        $params = ["id" => $_GET['publish']];
        if(!empty($sql_agent)) {
            $params["agent"] = $sql_agent_id;
        }
        $row = $db->fetch("SELECT * FROM `" . TABLE_BLOG_PINGS . "` WHERE `id` = :id;", $params);
    } catch (PDOException $e) {}

    if (!empty($row)) {
        try {
            // Publish Pingback
            $db->prepare("UPDATE `" . TABLE_BLOG_PINGS . "` SET `published` = 'true' WHERE `id` = :id;")->execute(["id" => $row['id']]);

            // Success
            $success[] = __('The selected blog ping has successfully been published.');

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('The selected blog ping could not be published.');
        }

    // Pingback not found
    } else {
        $errors[] = __('The selected blog ping could not be found.');
    }
}

// Filter Query
$_GET['filter'] = isset($_POST['filter']) ? $_POST['filter'] : $_GET['filter'];
$_GET['filter'] = !empty($_GET['filter']) ? $_GET['filter'] : 'pending';
if ($_GET['filter'] == 'published') {
    $published = "`published` = 'true'";
} elseif ($_GET['filter'] == 'pending') {
    $published = "`published` = 'false'";
} else {
    $published = "(`published` = 'false' OR `published` = 'true')";
}

$params = [];
if(!empty($sql_agent)) {
    $params["agent"] = $sql_agent_id;
}

// Count Pingbacks
try {
    $count_pings = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . TABLE_BLOG_PINGS . "` WHERE $published $sql_agent ORDER BY `timestamp_created` DESC;", $params);
} catch (PDOException $e) {}

if (!empty($count_pings['total'])) {
    // Page Limit
    if ($count_pings['total'] > 25) {
        $limitvalue = (($_GET['p'] - 1) * 25);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit  = " LIMIT " . $limitvalue . ", " . 25;
    }

    // Pagination
    $pagination = generate_pagination($count_pings['total'], $_GET['p'], 25);

    // Blog Pingbacks
    $pings = array();
    try {
        foreach ($db->fetchAll("SELECT *, UNIX_TIMESTAMP(`timestamp_created`) AS `date` FROM `" . TABLE_BLOG_PINGS. "` WHERE $published $sql_agent ORDER BY `timestamp_created` DESC" . $sql_limit, $params) as $manage_ping) {
            // Blog Entry
            try {
                $manage_ping['entry'] = $db->fetch("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `id` = :id;", ["id" => $manage_ping['entry']]);
            } catch (PDOException $e) {}

            // Blog Author
            try {
                $manage_ping['author'] = $db->fetch("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `" . TABLE_BLOG_AUTHORS . "` WHERE `id` = :id;", ["id" => $entry['agent']]);
            } catch (PDOException $e) {}

            // Add to Collection
            $pings[] = $manage_ping;
        }
    } catch (PDOException $e) {}
}

// Count Published/Un-Published Blog Pings
try {
    extract($db->fetch("SELECT SUM(FIELD(published, 'true')) `count_published`, SUM(FIELD(published, 'false')) `count_pending` FROM `" . TABLE_BLOG_PINGS . "` WHERE published in ('true', 'false') $sql_agent;", $params));
} catch (PDOException $e) {}
