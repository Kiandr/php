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

    // Require application bootstrap
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

// Cron Directories
$cron_path_backend = dirname(__FILE__);

// Include Common File
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

// File Cleanup
run_cron_file('php ' . $cron_path_backend . '/file_cleanup.php ' . escapeshellarg($root_dir) . ' ' . escapeshellarg($http_host) . ' ' . escapeshellarg($_SERVER['REQUEST_SCHEME']), 'File Cleanup');

// Calculate Script Execution Time
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

// Output
echo PHP_EOL . PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
