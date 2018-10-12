<?php

/**
 * Run a php file vie the command line
 *
 * @param string $file
 */
function run_cron_file($cmd, $title)
{
    // Run the file via system to keep its
    // variables seperate from the other crons
    echo 'Running ' . $title . PHP_EOL;
    echo '==================================================' . PHP_EOL . PHP_EOL;
    system(escapeshellcmd($cmd));
    echo '==================================================' . PHP_EOL . PHP_EOL;
}

$start = time();

// Make sure we are being run from the command line
if (isset($_SERVER['HTTP_HOST'])) {
    die('Not Authorized');
    //echo '<pre>' . PHP_EOL;

// Set ENV Variables
} else {
    /* Set HTTP Host & Document Root */
    $_SERVER['HTTP_HOST']     = basename($_SERVER['HOME']);

    // Require Composer Vendor Auto loader
    require_once __DIR__ . '/../../../boot/app.php';

    if (!Http_Host::isDev()) {
        $_SERVER['HTTP_HOST'] = 'www.' . $_SERVER['HTTP_HOST'];
        // Reset class caches
        Http_Host::isDev(true);
    }
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['HOME'] . '/app/httpdocs';

    // SSL
    $_SERVER['REQUEST_SCHEME'] = (Settings::getInstance()->SSL ? 'https' : 'http');
}
// HTTP Host
$http_host = $_SERVER['HTTP_HOST'];

// Root Directory
$root_dir = $_SERVER['DOCUMENT_ROOT'];
//$root_dir = dirname(__FILE__) . '/../..';

// Cron Directories
$cron_path_backend = dirname(__FILE__);

// Include Common File
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

// Campaign Mailer
run_cron_file('php ' . $cron_path_backend . '/campaign_emails.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host) . ' ' . escapeshellarg($_SERVER['REQUEST_SCHEME']), 'Campaign Mailer');

// Saved Searches
if (Settings::getInstance()->IDX_FEED != 'cms') {
    run_cron_file('php ' . $root_dir . '/idx/cron/savedsearches.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host) . ' ' . escapeshellarg($_SERVER['REQUEST_SCHEME']), 'IDX Saved Searches');
}

// Google Sitemap
 run_cron_file('php ' . $cron_path_backend . '/gsitemap.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host) . ' ' . escapeshellarg($_SERVER['REQUEST_SCHEME']), 'IDX Sitemap Generator');

// IDX Smart Searches
if (isset(Settings::getInstance()->MODULES['REW_IDX_CP']) && !empty(Settings::getInstance()->MODULES['REW_IDX_CP'])) {
    $db = DB::get();
    $result = $db->query("SELECT `auto_generated_searches` FROM `default_info` WHERE `auto_generated_searches` = 'true' AND `agent` = 1;");
    if ($result->fetch()) {
        run_cron_file('php ' . $root_dir . '/idx/cron/smartsearches.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host) . ' ' . escapeshellarg($_SERVER['REQUEST_SCHEME']), 'Smart Search Generator');
    }
}

// File Cleanup
run_cron_file('php ' . $cron_path_backend . '/file_cleanup.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host) . ' ' . escapeshellarg($_SERVER['REQUEST_SCHEME']), 'File Cleanup');

// Database Cleanup
run_cron_file('php ' . $cron_path_backend . '/database_cleanup.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host), 'Database Cleanup');

// Calculate Script Execution Time
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

// Output
echo PHP_EOL . PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
