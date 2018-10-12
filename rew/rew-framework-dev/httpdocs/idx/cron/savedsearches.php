<?php

// Set ENV Variables
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['DOCUMENT_ROOT'] = $argv[1];
    $_SERVER['HTTP_HOST'] = $argv[2];
}

$_SERVER['REQUEST_SCHEME'] = ($argv[3] === 'https' ? $argv[3] : 'http');

define('SAVEDSEARCH_DEBUG', ($argv[3] === 'debug' || $argv[4] === 'debug'));

// Include Config
$_GET['page'] = 'cron';
require_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';
@session_destroy();

// Get Settings
$settings = Settings::getInstance();

$indexTemplate = Container::getInstance()->get(\REW\Core\Interfaces\SkinInterface::class)->getSavedSearchEmailPath() . "index.php";
// Restricted Access
if (is_null($argv)) {
    if (!Settings::isREW()) {
        die("Access Denied!" . PHP_EOL);
    // Running as REW
    } else {
        echo '<h1>Running as REW</h1>' . PHP_EOL;
        echo '<pre>';
        if (empty($_GET['email'])) {
            echo 'Email Required' . PHP_EOL;
            exit;
        }
    }
}

// Only Use Lock File If Called Through CLI
if (PHP_SAPI == 'cli') {
    // Initialize Lock File
    Util_ProcessLock::setLockFile(__FILE__);

    // Check Whether Script Is Already Running
    if (Util_ProcessLock::isLocked()) {
        die("Saved Searches Are Already Being Processed" . PHP_EOL);
    }

    // Unlock The File At The End Of Script Execution
    register_shutdown_function(array('Util_ProcessLock', 'unlock'));
}


// Script Start Time
$start = time();

$available_feeds = !empty(Settings::getInstance()->IDX_FEEDS) ? array_keys(Settings::getInstance()->IDX_FEEDS) : array(Settings::getInstance()->IDX_FEED);

// Feeds That We Will Check For Listings As Required
$feeds = array();

try {
    // Gearman Client To Post Worker Requests
    $client = new GearmanClient();

    // Manages Workers To Ensure Consistent Execution
    $args = array($_SERVER['DOCUMENT_ROOT'], $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_SCHEME']);
    if (SAVEDSEARCH_DEBUG) {
        $args[] = 'debug';
    }
    $manager = new Gearman_WorkManager($client, __DIR__ . '/savedsearches-worker.php', 'process_saved_search', $args);

    $manager->startWorkersAsNecessary();

    $client->addServer();

    // Default Email Template
    // Generate Email Body (HTML Only)
    ob_start();
    include Page::locateTemplate('idx', 'emails', 'saved_searches');
    $default_saved_search_message = ob_get_contents();
    ob_end_clean();


    // Get CMS DB
    $db_users = DB::get();

    // Populate Feeds List
    foreach ($available_feeds as $feed) {
        $idx = Util_IDX::getIdx($feed);
        $db_idx = Util_IDX::getDatabase($feed);

        if ($updated = \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->lastUpdated($db_idx, $idx, true)) {
            // Get Last Script Run Time
            if ($last_script_run < $updated) {
                // Populate Feed Info With Last Updated Time
                $feeds[$feed] = array (
                    'last_updated' => $updated
                );

                //Add Saved Search Message To Feed Info
                $setting = $db_users->fetch("SELECT idxs.`savedsearches_responsive`,
                                                    idxs.`savedsearches_message`,
                                                    idxs.`savedsearches_responsive_params`,
                                                    idxs.`force_savedsearches_responsive`
                                             FROM `" . TABLE_IDX_SYSTEM . "` idxs
                                             WHERE idx in('" . $feed . "', '') ORDER BY FIELD(idx, '" . $feed . "', '') LIMIT 1;");
                if ($setting["force_savedsearches_responsive"] == 'true') {
                    $setting["savedsearches_responsive"] = 'true';
                }
                if ($setting["savedsearches_responsive"] == 'true') {
                    // Unserialize template settings
                    $setting["params"] = unserialize($setting["savedsearches_responsive_params"]);
                    $setting["params"]["listings"]["hero"] = !empty($settings['idx']['saved_searches']['hero_image']);
                }
                $setting['savedsearches_message'] = $setting['savedsearches_message'] ?: $default_saved_search_message;
                $feeds[$feed] = array_merge($feeds[$feed], $setting);
            }
        }
    }

    // Get List Of Active Feeds On Website
    $feed_names = array_keys($feeds);

    // Wrap Feed Names In Quotes For Upcoming Query
    array_walk($feed_names, function (&$value) {
        $value =  "'" . $value . "'";
    });


    // Users Searches Query
    $select = "SELECT
        `t1`.`id`,
        `t1`.`user_id`,
        `t1`.`title`,
        `t1`.`criteria`,
        `t1`.`idx`,
        `t1`.`frequency`,
        `t1`.`sent`,
        UNIX_TIMESTAMP(`t1`.`timestamp_sent`) AS `timestamp_sent`,
        `t2`.`first_name`,
        `t2`.`last_name`,
        `t2`.`email`,
        `t2`.`agent`,
        `t2`.`verified`,
        `t2`.`bounced`,
        `t2`.`fbl`,
        `t2`.`guid`,
        `t2`.`email_alt`,
        `t2`.`email_alt_cc_searches`";
    $from = " FROM `users_searches` `t1` JOIN `users` `t2` ON `t1`.`user_id` = `t2`.`id`";
    $where = " WHERE `t1`.`source_app_id` IS NULL
    AND `t2`.`opt_searches` = 'in'
    AND `t1`.`frequency` != 'never'
    AND `t1`.`frequency` != 'immediately'
    AND `t2`.`bounced` != 'true'
    AND `t2`.`fbl` != 'true'
    AND `t1`.`idx` IN (" . implode(',', $feed_names) . ")
    AND ( `t1`.`timestamp_idx` IS NULL
        OR
        CASE `t1`.`frequency`
            WHEN 'monthly'   THEN `t1`.`timestamp_idx` <= DATE_SUB(NOW(), INTERVAL 1 MONTH)
            WHEN 'weekly'    THEN `t1`.`timestamp_idx` <= DATE_SUB(NOW(), INTERVAL 1 WEEK)
            WHEN 'daily'     THEN `t1`.`timestamp_idx` <= DATE_SUB(NOW(), INTERVAL 1 DAY)
        END
    )" . (!empty($_GET['email']) ? " AND `t2`.`email` = '". $_GET['email'] . "'" : '');
    $order = " ORDER BY `t2`.`agent` ASC";

    // Build Query
    $query = $select . $from . $where. $order;

    // Get Users Searches
    if ($searches = $db_users->query($query)) {
        // Change Out Select To Count Number Of Valid Users Searches
        $select = "SELECT COUNT(`t1`.`id`) as 'count'";

        // Rebuild Query
        $query = $select . $from . $where . $order;

        // Get Count
        $search_count = $db_users->fetch($query);

        echo "Queuing tasks" . PHP_EOL;

        $handles = array();

        $manager->setTotalTasks($search_count['count']);

        // For Each User's Search, Run It In The Background
        while ($search = $searches->fetch(PDO::FETCH_ASSOC)) {
            // Check If E-Mail Host Is Blocked
            if (Validate::verifyWhitelisted($search['email'])) {
                echo $search['email'] . '\'s e-mail provider is on the server block list - skipping automated e-mail' . PHP_EOL;
                continue;
            }

            // Check If E-Mail Host Requires Verification
            if (Validate::verifyRequired($search['email']) || !empty(Settings::getInstance()->SETTINGS['registration_verify'])) {
                if ($search['verified'] != 'yes') {
                    echo $search['email'] . '\'s e-mail provider is set to require e-mail verification on this server, but the account has not been verified yet - skipping automated e-mail' . PHP_EOL;
                    continue;
                }
            }

            if (empty($agent) || $agent->getId() != $search['agent']) {
                $agent = Backend_Agent::load($search['agent']);
            }

            $uuid = $db_users->fetch("SELECT UUID() UUID;");

            // Queues Job And Runs In The Background
            $manager->runTaskInBackground(array(
                'search'               => $search,
                'agent'                => $agent->getRow(),
                'settings'             => $feeds[$search['idx']],
                'uuid'                 => $uuid["UUID"],
                'indexTemplate'        => $indexTemplate
            ));
        }

        // Wait For The Remaining Queued Jobs To Finish Before Continuing
        $manager->monitorWorkerQueue();
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
} finally {

    // Get CMS DB
    $db_users = DB::get();

    // Print Log Output
    $logs = $db_users->query("SELECT type, message FROM saved_search_logger ORDER BY `group_id` ASC, `id` ASC");
    while ($log = $logs->fetch(PDO::FETCH_ASSOC)) {
        echo $log['type'] . ': ' . $log['message'] . PHP_EOL;
    }

    // Empty Log
    $db_users->query("TRUNCATE TABLE saved_search_logger");

}

// Calculate Script Execution Time
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

// Output
echo "\n" . "Running time: " . $hours . " hrs, " . $minutes . " mins, " . $seconds . " secs\n";
