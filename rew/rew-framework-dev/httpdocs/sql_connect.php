<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\Util\CMSInterface as UtilCMSInterface;

/**
 * Handle IP if running via proxy
 */
if (isset($_SERVER['HTTP_X_REAL_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}

// Script being called directly so force forbidden response
if (str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__) === $_SERVER['SCRIPT_NAME']) {
    header('Content-Type: text/plain', true, 403);
    exit;
}

// Require Composer Vendor Auto loader
require_once dirname(__FILE__) . '/../boot/app.php';
if(!($app instanceof Container)) {
    global $appBkp;
	$app = $appBkp;
}

// Start Profile Session
Profile::startSession(!isset($_GET['REW_PROFILE_DISABLE']) && Settings::isREW()
    ? Profile::PROFILE_MODE_DEVELOPMENT
    : Profile::PROFILE_MODE_PRODUCTION);

// Start Session
@session_start();

// Profile start
$timer_sql = Profile::timer()->stopwatch('Include <code>/sql_connect.php</code>')->start();

// DB Connection
$db = $app->get(DBInterface::class);

// Get & Set Timezone based on Super Admin's settings
$timer = Profile::timer()->stopwatch('Get/Set Timezone')->start();
$timezone = $db->query("SELECT `t`.`TZ` FROM `agents` `a` LEFT JOIN `timezones` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`id` = 1 LIMIT 1;");
$timezone = $timezone->fetchColumn();
if (!empty($timezone)) {
    // Set PHP Timezone
    date_default_timezone_set($timezone);
    // Set MySQL Timezone (PDO)
    $db->query("SET `time_zone` = '" . $timezone . "';");
}
$timer->stop();

$utilCMS = $app->get(UtilCMSInterface::class);
$idxFactory = $app->get(IDXFactoryInterface::class);

// Check Subdomain
$utilCMS->checkSubdomain();

// Check Redirects
$utilCMS->checkRedirects();

// Load IDX Settings
$idxFactory->loadSettings();

// Profile end
$timer_sql->stop();
