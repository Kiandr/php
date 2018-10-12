<?php

// Get Database
$db = DB::get();

// Create Auth Classes
$toolsAuth = new REW\Backend\Auth\ToolsAuth(Settings::getInstance());
if (!$toolsAuth->canManageRewrites($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage rewrites.')
    );
}

// Success Collection
$success = array();

// Error Collection
$errors = array();

// Hide Form
$show_form = false;

// Delete Row
if (!empty($_GET['delete'])) {
    // Select Row
    try {
        $delete = $db->fetch("SELECT * FROM `" . TABLE_REWRITES . "` WHERE `id` = :id;", ["id" => $_GET['delete']]);
    } catch (PDOException $e) {}

    // Require Row
    if (empty($delete)) {
        $errors[] = __('The selected redirect could not be found. Please try again.');
    } else {
        // Delete Row
        try {
            $db->prepare("DELETE FROM `" . TABLE_REWRITES . "` WHERE `id` = :id LIMIT 1;")
                ->execute([
                    "id" => $delete['id']
                ]);
            $success[] = __('The selected redirect has successfully been deleted.');

        // Query Error
        } catch (PDOException $e) {
            $errors[] = __('The selected redirect could not be deleted.');
        }
    }
}

// Add Row
$_GET['add'] = isset($_POST['add']) ? $_POST['add'] : $_GET['add'];
if (isset($_GET['add'])) {
    // Show Form
    $show_form = true;

    // Process Submit
    if (isset($_GET['submit'])) {
        // Required Fields
        $required = array();
        $required[] = array('value' => 'old', 'title' => __('Old Filename'));
        $required[] = array('value' => 'new', 'title' => __('New Filename'));

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = __('%s is a required field.', $require['title']);
            }
        }

        // Check that old starts with a /
        if (strpos($_POST['old'], '/') !== 0) {
            $errors[] = __('The old filename must begin with /.');
        }

        // Check that old starts with a /
        if (strpos($_POST['new'], '/') !== 0 && preg_match('/^http:\/\//', $_POST['new']) == 0) {
            $errors[] = __('The new filename must begin with / or http://.');
        }

        // Check for simple loops
        if ($_POST['old'] == $_POST['new']) {
            $errors[] = __('The old and new filenames are identical and will create a loop.');
        }

        // Check Duplicate Old Filename
        try {
            $duplicate = $db->fetch("SELECT * FROM `" . TABLE_REWRITES . "` WHERE BINARY `old` = :old LIMIT 1;", ["old" => $_POST['old']]);
        } catch (PDOException $e) {}
        if (!empty($duplicate)) {
            $errors[] = __('A Redirect Rule already exists for %s.', $_POST['old']);
        }

        // Check Errors
        if (empty($errors)) {
            try {
                // Build INSERT Query
                $db->prepare("INSERT INTO `" . TABLE_REWRITES . "` SET "
                       . "`old` = :old, "
                       . "`new` = :new, "
                       . "`timestamp_created` = NOW();")
                ->execute([
                    "old" => $_POST['old'],
                    "new" => $_POST['new']
                ]);

                // Success
                $success[] = __('Redirect Rule has successfully been created.');

                // Hide Form
                $show_form = false;

            // Query Error
            } catch (PDOException $e) {
                $errors[] = __('Error occurred, Redirect Rule could not be created.');
            }
        }
    }
}

// Edit Row
$_GET['edit'] = isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'];
if (!empty($_GET['edit'])) {
    // Select Row
    try {
        $edit = $db->fetch("SELECT * FROM `" . TABLE_REWRITES . "` WHERE `id` = :id LIMIT 1;", ["id" => $_GET['edit']]);
    } catch (PDOException $e) {}

    // Require Row
    if (empty($edit)) {
        $errors[] = __('The selected redirect could not be found. Please try again.');
    } else {
        // Process Submit
        if (isset($_GET['submit'])) {
            // Required Fields
            $required = array();
            $required[] = array('value' => 'old', 'title' => __('Old Filename'));
            $required[] = array('value' => 'new', 'title' => __('New Filename'));

            // Process Required Fields
            foreach ($required as $require) {
                if (empty($_POST[$require['value']])) {
                    $errors[] = __('%s is a required field.', $require['title']);
                }
            }

            // Check that old starts with a /
            if (strpos($_POST['old'], '/') !== 0) {
                $errors[] = __('The old filename must begin with /.');
            }

            // Check that old starts with a /
            if (strpos($_POST['new'], '/') !== 0 && preg_match('/^https?:\/\//', $_POST['new']) == 0) {
                $errors[] = __('The new filename must begin with / or http:// or https://.');
            }

            // Check for simple loops
            if ($_POST['old'] == $_POST['new']) {
                $errors[] = __('The old and new filenames are identical and will create a loop.');
            }

            // Check Duplicate Old Filename
            try {
                $duplicate = $db->fetch("SELECT * FROM `" . TABLE_REWRITES . "` WHERE BINARY `old` = :old AND `id` != :id LIMIT 1;", ["old" => $_POST['old'], "id" => $edit['id']]);
            } catch (PDOException $e) {}
            if (!empty($duplicate)) {
                $errors[] = __('A Redirect Rule already exists for %s.', $_POST['old']);
            }

            // Check Errors
            if (empty($errors)) {
                try {
                    // Build UPDATE Query
                    $db->prepare("UPDATE `" . TABLE_REWRITES . "` SET "
                           . "`old`			   = :old, "
                           . "`new`			   = :new, "
                           . "`timestamp_updated` = NOW()"
                           . " WHERE "
                           . "`id` = :id;")
                    ->execute([
                        "old" => $_POST['old'],
                        "new" => $_POST['new'],
                        "id" => $edit['id'],
                    ]);

                    // Success
                    $success[] = __('Redirect Rule has successfully been saved.');

                    // Unset Row
                    unset($edit);

                // Query Error
                } catch (PDOException $e) {
                    $errors[] = __('Error occurred, Redirect Rule could not be saved.');
                }
            }
        }
    }
}

// Rewrites
$rewrites = array();

// Pagination
// Cursor details
$beforeCursor = $_GET['before'];
$afterCursor = $_GET['after'];
$primaryKey = 'id';
$searchLimit = 10;
$orderBy = 'old';
$sortDir = 'ASC';

// Next
if (!empty($afterCursor)) {
    $cursor = REW\Pagination\Cursor\After::decode($afterCursor);

    // Prev
} else if (!empty($beforeCursor)) {
    $cursor = REW\Pagination\Cursor\Before::decode($beforeCursor);

    // First
} else {
    $cursor = new REW\Pagination\Cursor($primaryKey, $searchLimit, $orderBy, $sortDir);

}

// Create pagination instance
$pagination = new REW\Pagination\Pagination($cursor);

$limit = $pagination->getLimit();
$limitQuery = $limit ? " LIMIT " . $limit : "";
$order = $pagination->getOrder();
$orderQuery = "";
foreach ($order as $sort => $dir) {
    $sortString = "`" . $sort . "` ";
    // Need to CAST field `published` to a CHAR as it is an enum and can cause ordering issues
    if ($sort === 'published') $sortString = "CAST(`" . $sort . "` AS CHAR) ";
    $orderQuery .= $sortString . $dir . ", ";
};
$orderQuery = rtrim(" ORDER BY " . $orderQuery, ", ");
$paginationWhere = $pagination->getWhere();
$paramsPagination = $pagination->getParams();
if (!empty($paginationWhere)) {
    $paginationWhere = " WHERE " . $paginationWhere;
}

// Select Rows
try {
    $rewrites = $db->fetchAll("SELECT * FROM `" . TABLE_REWRITES . "`" . $paginationWhere . $orderQuery . $limitQuery . ";", $paramsPagination);
} catch (PDOException $e) {
    // Query Error
    $errors[] = __('Error Occurred while Loading Redirect Rules');
}

$pagination->processResults($rewrites);

for ($i = 0; $i < count($rewrites); $i++) {
    // Delete Link
    $deleteLink = sprintf('delete=%s%s', $rewrites[$i]['id'], $appendFilter);
    if (!empty($_GET['after'])) {
        $rewrites[$i]['deleteLink'] = sprintf('?after=%s&%s', $_GET['after'], $deleteLink);
    } else if (!empty($_GET['before'])) {
        $rewrites[$i]['deleteLink'] = sprintf('?before=%s&%s', $_GET['before'], $deleteLink);
    } else {
        $rewrites[$i]['deleteLink'] = sprintf('?%s', $deleteLink);
    }
}

// Pagination link URLs
$nextLink = $pagination->getNextLink();
$prevLink = $pagination->getPrevLink();
$paginationLinks = ['nextLink' => $nextLink, 'prevLink' => $prevLink];

// Default $_POST
$_POST['old'] = !empty($_POST['old']) ? $_POST['old'] : '/';
$_POST['new'] = !empty($_POST['new']) ? $_POST['new'] : '/';
