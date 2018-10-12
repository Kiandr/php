<?php

/* Restrict Access */
if (isset($_SERVER['HTTP_HOST'])) {
    // Require Composer Vendor Auto loader
    require_once __DIR__ . '/../../../boot/app.php';

    // Running from REW Office
    if (Settings::isREW()) {
        // Serve as Plaintext
        header('Content-Type: text/plain');
    } else {
        // Not Authorized
        die('Not Authorized');
    }

/* Set ENV Variables */
} else {
    /* Set HTTP Host & Document Root */
    $_SERVER['DOCUMENT_ROOT'] = $argv[1];
    $_SERVER['HTTP_HOST'] = $argv[2];
}

/* Start Time */
$start = time();

/* Include Common File */
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

/**
 * Cleanup History Events
 */
echo PHP_EOL . 'Old History Events Cleanup and old Users Sessions:' . PHP_EOL;

// Queries to Execute
$queries = array();

// Delete Login events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'Login' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Logout events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'Logout' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete SavedListing events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'SavedListing' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete SavedSearch events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'SavedSearch' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete ViewedLead events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'ViewedLead' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete ViewedListing events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'ViewedListing' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete ViewedSearch events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Action' AND `subtype` = 'ViewedSearch' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete LeadNote events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Create' AND `subtype` = 'LeadNote' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete LeadReminder events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Create' AND `subtype` = 'LeadReminder' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Assign events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'Assign' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete GroupAdd events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'GroupAdd' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete GroupRemove events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'GroupRemove' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Lead events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'Lead' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Status events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'Status' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete UnAssign events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'UnAssign' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Lead events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Delete' AND `subtype` = 'Lead' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete SavedListing events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Delete' AND `subtype` = 'SavedListing' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete SavedSearch events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Delete' AND `subtype` = 'SavedSearch' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete AutoResponder events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Email' AND `subtype` = 'AutoResponder' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Campaign events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Email' AND `subtype` = 'Campaign' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Delayed events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Email' AND `subtype` = 'Delayed' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Listings events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Email' AND `subtype` = 'Listings' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Reminder events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Email' AND `subtype` = 'Reminder' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Email Sent events older than 12 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'Email' AND `subtype` = 'Sent' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete old Sessions
$queries[] = "DELETE FROM `users_sessions` WHERE `timestamp` < NOW() - INTERVAL 12 MONTH";

// Delete Legacy Note events older than 24 months
$queries[] = "DELETE FROM `history_events` WHERE `type` = 'LegacyNote' AND `subtype` = 'LegacyHistory' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
    // Delete ActionPlanAssign events older than 12 months
    $queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'ActionPlanAssign' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

    // Delete ActionPlanUnAssign events older than 12 months
    $queries[] = "DELETE FROM `history_events` WHERE `type` = 'Update' AND `subtype` = 'ActionPlanUnAssign' AND `timestamp` < NOW() - INTERVAL 12 MONTH";

    // Delete resolved tasks older than 12 months
    $queries[] = "DELETE FROM `" . TABLE_USERS_TASKS . "` WHERE `status` IN ('Completed','Expired','Dismissed') AND `timestamp_resolved` < NOW() - INTERVAL 12 MONTH";
}

foreach ($queries as $query) {
    echo 'Query: ' . "\t" . $query . PHP_EOL;
    $total_deleted = 0;

    do {
        $_start = microtime(true);

        /* Execute Query */
        if (mysql_query($query . " LIMIT 1000")) {
            // Execute Query
            $deleted_loop = mysql_affected_rows();
            $total_deleted += $deleted_loop;

            // Timing
            echo 'Time: ' . "\t" . (microtime(true) - $_start) . ' seconds' . PHP_EOL;
            echo 'Rows: ' . "\t" . number_format($total_deleted) . PHP_EOL;

            // Sleep after long queries to help reduce server load
            $sleeptime = intval(microtime(true) - $_start);
            if ($sleeptime > 0) {
                echo 'Sleeping to let the server rest' . PHP_EOL;
                sleep($sleeptime);
            }
        } else {
            /* Error */
            echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
        }
    } while ($deleted_loop == 1000);
}

// Clean up orphaned history_events
echo date("[g:i:s A] ") . "Clean up orphaned history_events" . PHP_EOL;
$total_deleted = 0;
do {
    $del_ids = array();

    $deleted_loop = 0;

    echo date("[g:i:s A] ") . "Select Chunk Start" . PHP_EOL;
    $sql = "SELECT `he`.`id` FROM `history_events` he LEFT JOIN  `history_users` hu ON  `he`.`id` =  `hu`.`event` WHERE  `hu`.`id` IS NULL LIMIT 1000;";

    $_start = microtime(true);

    foreach ($db->query($sql) as $page_id) {
        $del_ids[] = $page_id['id'];
    }

    echo date("[g:i:s A] ") . "Select Chunk End" . PHP_EOL;

    if (!empty($del_ids)) {
        $sql = "DELETE FROM `history_events` WHERE `id` IN (" . implode(', ', $del_ids) . ")";
        echo date("[g:i:s A] ") . "Delete Chunk Start" . PHP_EOL;

        $deleted_loop = $db->exec($sql);
        $total_deleted += $deleted_loop;

        echo date("[g:i:s A] ") . "Delete Chunk End" . PHP_EOL;
    }

    // Sleep after long queries to help reduce server load
    $sleeptime = intval(microtime(true) - $_start);
    if ($sleeptime > 0) {
        echo 'Sleeping to let the server rest' . PHP_EOL;
        sleep($sleeptime);
    }


    echo date("[g:i:s A] ") . number_format($deleted_loop) . " records removed. " . number_format($total_deleted) . " Total." . PHP_EOL;
} while ($deleted_loop == 1000);

/**
 * Remove Unknown Sessions
 */
echo PHP_EOL. 'Removing Stagnant Sessions:' . PHP_EOL . PHP_EOL;

/* Build Query */
$query = "DELETE FROM `" . LM_TABLE_VISITS . "` WHERE `timestamp` < DATE_SUB(NOW(), INTERVAL 3 DAY) AND `user_id` IS NULL;";

/* Execute Query */
if (mysql_query($query)) {
    /* Success */
    echo "\t" . 'Rows Removed: ' . mysql_affected_rows() . PHP_EOL;
} else {
    /* Error */
    echo "\t" . 'Query: ' . $query . PHP_EOL;
    echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
}

echo PHP_EOL . "Cleaning users_pageviews" . PHP_EOL;
$total_deleted = 0;
do {
    $del_ids = array();

    $deleted_loop = 0;

    echo date("[g:i:s A] ") . "Select Chunk Start" . PHP_EOL;
    $sql = "SELECT `pv`.`id` FROM `users_pageviews` pv LEFT JOIN `users_sessions` s ON `pv`.`session_id` = `s`.`id` WHERE `s`.`id` IS NULL LIMIT 1000;";

    $_start = microtime(true);
    $page_res = mysql_query($sql) or die(mysql_error());
    echo date("[g:i:s A] ") . "Select Chunk End" . PHP_EOL;

    while ($page_id = mysql_fetch_assoc($page_res)) {
        $del_ids[] = $page_id['id'];
    }

    if (!empty($del_ids)) {
        $sql = "DELETE FROM `users_pageviews` WHERE `id` IN (" . implode(', ', $del_ids) . ")";
        echo date("[g:i:s A] ") . "Delete Chunk Start" . PHP_EOL;

        mysql_query($sql) or die(mysql_error());
        $deleted_loop = mysql_affected_rows();
        $total_deleted += $deleted_loop;

        echo date("[g:i:s A] ") . "Delete Chunk End" . PHP_EOL;
    }

    // Sleep after long queries to help reduce server load
    $sleeptime = intval(microtime(true) - $_start);
    if ($sleeptime > 0) {
        echo 'Sleeping to let the server rest' . PHP_EOL;
        sleep($sleeptime);
    }

    echo date("[g:i:s A] ") . number_format($deleted_loop) . " rows removed. " . number_format($total_deleted) . " Total." . PHP_EOL;
} while ($deleted_loop == 1000);

echo PHP_EOL . "Cleaning users_pages" . PHP_EOL;
$total_deleted = 0;
do {
    $del_ids = array();

    $deleted_loop = 0;

    echo date("[g:i:s A] ") . "Select Chunk Start" . PHP_EOL;
    $sql = "SELECT `p`.`id` FROM `users_pages` p LEFT JOIN `users_pageviews` pv1 ON `p`.`id` = `pv1`.`page_id` LEFT JOIN `users_pageviews` pv2 ON `p`.`id` = `pv2`.`referer_id` WHERE `pv1`.`page_id` IS NULL AND `pv2`.`referer_id` IS NULL LIMIT 1000;";

    $_start = microtime(true);
    $page_res = mysql_query($sql) or die(mysql_error());
    echo date("[g:i:s A] ") . "Select Chunk End" . PHP_EOL;

    while ($page_id = mysql_fetch_assoc($page_res)) {
        $del_ids[] = $page_id['id'];
    }

    if (!empty($del_ids)) {
        $sql = "DELETE FROM `users_pages` WHERE `id` IN (" . implode(', ', $del_ids) . ")";
        echo date("[g:i:s A] ") . "Delete Chunk Start" . PHP_EOL;

        mysql_query($sql) or die(mysql_error());
        $deleted_loop = mysql_affected_rows();
        $total_deleted += $deleted_loop;

        echo date("[g:i:s A] ") . "Delete Chunk End" . PHP_EOL;
    }

    // Sleep after long queries to help reduce server load
    $sleeptime = intval(microtime(true) - $_start);
    if ($sleeptime > 0) {
        echo 'Sleeping to let the server rest' . PHP_EOL;
        sleep($sleeptime);
    }


    echo date("[g:i:s A] ") . number_format($deleted_loop) . " URLs removed. " . number_format($total_deleted) . " Total." . PHP_EOL;
} while ($deleted_loop == 1000);

/**
 * Remove Sent Delayed Emails
 */
echo PHP_EOL . 'Removing Sent Delayed Emails:' . PHP_EOL . PHP_EOL;

/* Build Query */
$query = "DELETE FROM `" . LM_TABLE_DELAYED_EMAILS . "` WHERE `timestamp` < DATE_SUB(NOW(), INTERVAL 3 DAY) AND `sent` = 'Y';";

/* Execute Query */
if (mysql_query($query)) {
    /* Success */
    echo "\t" . 'Rows Removed: ' . mysql_affected_rows() . PHP_EOL;
} else {
    /* Error */
    echo "\t" . 'Query: ' . $query . PHP_EOL;
    echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
}

/**
 * Cleanup Viewed Searches (Only Keep Last 10 Searches per Lead)
 */
echo PHP_EOL . 'Viewed Searches Cleanup:' . PHP_EOL;

/* Build Query */
$query = "SELECT COUNT(`id`) AS `total`, `user_id` FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `agent_id` IS NULL GROUP BY `user_id` HAVING `total` > 10;";

/* Execute Query */
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        /* Output */
        echo PHP_EOL . "\t" . 'Lead #' . $row['user_id'] . ': ' . $row['total'] . PHP_EOL;

        /* Build DELETE Query */
        $query = "DELETE FROM `" . TABLE_VIEWED_SEARCHES . "` WHERE `agent_id` IS NULL AND `user_id` = '" . $row['user_id'] . "' ORDER BY `timestamp` ASC LIMIT " . ($row['total'] - 10) . ";";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Success */
            echo "\t" . 'Rows Removed: ' . mysql_affected_rows() . PHP_EOL;
        } else {
            /* Error */
            echo "\t" . 'Query: ' . $query . PHP_EOL;
            echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
        }
    }
} else {
    /* Error */
    echo "\t" . 'Query: ' . $query . PHP_EOL;
    echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
}

/**
 * Cleanup Viewed Listings (Only Keep Last 20 Viewed Listings per Lead)
 */
echo PHP_EOL . 'Viewed Listings Cleanup:' . PHP_EOL;

/* Build Query */
$query = "SELECT COUNT(`id`) AS `total`, `user_id`, `idx` FROM `" . TABLE_VIEWED_LISTINGS . "` GROUP BY `user_id`, `idx` HAVING `total` > 20;";

/* Execute Query */
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        /* Output */
        echo PHP_EOL . "\t" . 'Lead #' . $row['user_id'] . ': ' . $row['total'] . PHP_EOL;

        /* Build DELETE Query */
        $query = "DELETE FROM `" . TABLE_VIEWED_LISTINGS . "` WHERE `user_id` = '" . $row['user_id'] . "' AND `idx` = '" . $row['idx'] . "' ORDER BY `timestamp` ASC LIMIT " . ($row['total'] - 20) . ";";

        /* Execute Query */
        if (mysql_query($query)) {
            /* Success */
            echo "\t" . 'Rows Removed: ' . mysql_affected_rows() . PHP_EOL;
        } else {
            /* Error */
            echo "\t" . 'Query: ' . $query . PHP_EOL;
            echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
        }
    }
} else {
    /* Error */
    echo "\t" . 'Query: ' . $query . PHP_EOL;
    echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
}

/**
 * Cleanup logged API requests (Only keep last 30 days)
 */

echo PHP_EOL . 'API Requests Cleanup:' . PHP_EOL;

// Build query
$query = "DELETE FROM `api_requests` WHERE `timestamp` < DATE_SUB(NOW(), INTERVAL 30 DAY);";

// Execute Query
if (mysql_query($query)) {
    // Success
    echo "\t" . 'Rows Removed: ' . mysql_affected_rows() . PHP_EOL;

    // Update app request counts
    $sql = "SELECT `id` FROM `api_applications`;";
    if ($apps_result = mysql_query($sql)) {
        while ($app_row = mysql_fetch_assoc($apps_result)) {
            $count_ok = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS `total` FROM `api_requests` WHERE `app_id` = '" . $app_row['id'] . "' AND `status` = 'ok';"));
            $count_error = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS `total` FROM `api_requests` WHERE `app_id` = '" . $app_row['id'] . "' AND `status` = 'error';"));
            mysql_query("UPDATE `api_applications` SET `num_requests_ok` = '" . $count_ok['total'] . "', `num_requests_error` = '" . $count_error['total'] . "' WHERE `id` = '" . $app_row['id'] . "';");
        }
    }
} else {
    // Error
    echo "\t" . 'Query: ' . $query . PHP_EOL;
    echo "\t" . 'Query Error: ' . mysql_error() . PHP_EOL;
}

/**
 * Cleanup Orphaned CMS Uploads (No Associated Listing, Directory Listing, Lender, or Featured Community As It Was Never Created)
 */
echo PHP_EOL . 'Cleaning CMS Uploads:' . PHP_EOL;

// Track Deleted
$deleted = 0;
$orphans = false;

try {
    // Build Query
    $db = DB::get();
    $files = $db->prepare(
        "SELECT `u`.`id`, `u`.`type`, `u`.`row`, `u`.`file`, `u`.`size` " .
        "FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` `u` " .
        "WHERE `u`.`row` IS NULL " .
        "AND `u`.`type` IN ('listing', 'directory', 'directory_logo', 'community', 'lender')"
    );

    //Execute Query
    $files->execute();

    // Prepare Upload Deletion
    $stmt = $db->prepare("DELETE FROM `" . Settings::getInstance()->TABLES['UPLOADS'] . "` WHERE `id` = :id;");

    while ($file = $files->fetch()) {
        $orphans = true;

        // Full Path to File
        $path = Settings::getInstance()->DIRS['UPLOADS'] . $file['file'];

        // Remove Upload
        $stmt->execute(['id' => $file['id']]);

        // Remove File From File System
        @unlink($path);

        // Output
        echo PHP_EOL;
        echo "\t" . 'Upload Deleted: ' . $file['file'] . PHP_EOL;
        echo "\t" . 'Type: ' . $file['type'] . PHP_EOL;
        echo "\t" . 'Size: ' . Format::filesize($file['size']) . PHP_EOL;

        // Increment
        $deleted++;
    }
} catch (PDOException $e) {
    // Query Error
    echo "\t" . 'ERROR: ' . $e->getMessage() . PHP_EOL;
}

if ($orphans) {
    // Output
    echo PHP_EOL . "\t" . 'Total Deleted: ' . number_format($deleted) . PHP_EOL;
}

/* Calculate Script Execution Time */
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

/* Output */
echo PHP_EOL . PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
