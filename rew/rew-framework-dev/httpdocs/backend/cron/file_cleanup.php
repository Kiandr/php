<?php

/* Restrict Access */
if (isset($_SERVER['HTTP_HOST'])) {
        // Require application bootstrap
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
    $_SERVER['REQUEST_SCHEME'] = $argv[3];
}

/* Start Time */
$start = time();

/* Include Common File */
$_GET['page'] = 'cron';
include_once dirname(__FILE__) . '/../common.inc.php';
@session_destroy();

/**
 * Cache Cleanup
 */

// Maximum cache size - if purge is set then use 0 otherwise default to 25 megs
$sizeLimit = !empty($_GET['purge'])? 0 : 1024 * 1024 * 25;

// Output
echo '-------------------------' . PHP_EOL;
echo '-- Cleaning site cache --' . PHP_EOL;
echo '-------------------------' . PHP_EOL;
echo 'Size limit: ' . Format::filesize($sizeLimit) . PHP_EOL;

// Process cache folders
$cacheDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/inc/cache');
foreach (glob($cacheDir . '/*', GLOB_ONLYDIR) as $dir) {
        $files = null;

        // Only process certain cache folders
        $dirName = basename($dir);
    if (!in_array($dirName, array('css', 'js', 'html', 'img', 'tmp'))) {
        continue;
    }

        // Check disk usage
        $totalSize = exec('du -b --max-depth=0 ' . escapeshellarg($dir) . ' |  awk \'{print $1}\';');

        // Output
        echo PHP_EOL . 'Cache path: ' . $dirName . PHP_EOL;
        echo PHP_EOL . "\t" . 'Total size: ' . Format::filesize($totalSize) . PHP_EOL;

        // Cache size is within limit
    if ($totalSize < $sizeLimit) {
            echo "\t" . strtoupper('Within Cache Limit') . PHP_EOL;
            continue;
    }

        // Output
        echo PHP_EOL . "\t" . strtoupper('Exceeded Cache Limit of '.Format::filesize($sizeLimit)) . PHP_EOL;

        // Track progress
        $cleanSpace = 0;
        $deleted = 0;

        // Find files by access time
        $cmd = 'find ' . escapeshellarg($dir) . ' -type f -printf "%A+::%p::%s\n" | grep -v .svn';
        echo 'Executing command: ' . PHP_EOL . $cmd . PHP_EOL;
        exec($cmd, $files);
    if (!empty($files)) {
            echo PHP_EOL . "\t" . 'Files found: ' . Format::number(count($files));
        foreach ($files as $file) {
                list ($time, $path, $size) = explode('::', $file);
            if (substr(basename($path), 0, 1) === '.') {
                continue;
            }
            if ($cleanSpace > $totalSize - ($sizeLimit / 2)) {
                break;
            }
            if (unlink($path)) {
                    $cleanSpace += $size;
                    $deleted++;
            }
        }
    }

        // Output
        echo PHP_EOL . "\t" . 'Removed files: ' . Format::number($deleted);
        echo PHP_EOL . "\t" . 'Cleaned space: ' . Format::filesize($cleanSpace);
        echo PHP_EOL;
}

/**
 * Cache Cleaner: File System
 */
echo PHP_EOL . 'Generating Error Page:' . PHP_EOL;

$command = 'wget --no-check-certificate -T 5 -t 1 -O %s/inc/cache/html/error.html %s://%s/error.php 2>&1';
$command = sprintf($command, $_SERVER['DOCUMENT_ROOT'], $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST']);
system($command, $error);

if (!empty($error)) {
        system('echo ' . escapeshellarg('<h1>503: Service Temporarily Unavailable</h1><p>We are sorry – there appears to have been an error processing your request. Simply try refreshing the page, or going back to the home page.</p><p>{error}</p>') . ' > ' . $_SERVER['DOCUMENT_ROOT'] . '/inc/cache/html/error.html');
}
system('echo '.escapeshellarg('<script type="text/javascript">$.ajax = function() {};</script>').' >> ' . $_SERVER['DOCUMENT_ROOT'] . '/inc/cache/html/error.html');

/* Calculate Script Execution Time */
$runTime = time() - $start;
$hours    = floor($runTime / 3600);
$runTime -= ($hours * 3600);
$minutes  = floor($runTime / 60);
$runTime -= ($minutes * 60);
$seconds  = $runTime;

/* Output */
echo PHP_EOL . PHP_EOL . 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs.' . PHP_EOL;
