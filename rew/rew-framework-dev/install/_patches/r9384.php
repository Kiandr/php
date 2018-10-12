<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Connection settings
$settings = DB::settings();

// DB connection
$db = DB::get();

$query = $db->prepare("
SELECT *
FROM information_schema.COLUMNS
WHERE
TABLE_SCHEMA = :db_name
AND TABLE_NAME = 'default_info'
AND COLUMN_NAME = 'summary';
");
$query->execute(array('db_name' => $settings['database']));
$homepage_summary = $query->fetch();

if (!empty($homepage_summary)) {
	$query = $db->prepare("SELECT `summary` FROM `default_info` WHERE `summary` NOT LIKE ''");
	$query->execute();
	$homepage_summary = $query->fetch();
}

$query = $db->prepare("
SELECT *
FROM information_schema.COLUMNS
WHERE
TABLE_SCHEMA = :db_name
AND TABLE_NAME = 'pages'
AND COLUMN_NAME = 'summary';
");
$query->execute(array('db_name' => $settings['database']));
$pages_summary = $query->fetch();

if(!empty($pages_summary)) {
	$query = $db->prepare("SELECT `summary` FROM `pages` WHERE `summary` NOT LIKE ''");
	$query->execute();
	$pages_summary = $query->fetch();
}

$query = $db->prepare("SHOW TABLES LIKE 'rewidx_comments'");
$query->execute(array('db_name' => $settings['database']));
$comments = $query->fetch();

if (!empty($comments)) {
	$query = $db->prepare("SELECT `id` FROM `rewidx_comments`");
	$query->execute();
	$comments = $query->fetch();
}

// If there is data in any of the fields we are about to remove, back up their tables
if (!empty($homepage_summary) || !empty($pages_summary) || !empty($comments)) {

	// Back up tables
	$command = 'mysqldump -u ' . escapeshellarg($settings['username']) . ' -p' . escapeshellarg($settings['password']) . ' ' . escapeshellarg($settings['database']) . ' ' . (!empty($homepage_summary) ? 'default_info ' : '') . (!empty($pages_summary) ? 'pages ' : '') . (!empty($comments) ? 'rewidx_comments ' : '') . '| gzip > ' . __DIR__ . '/../../r9384.sql.gz';
	exec($command, $output, $error);
	if (!empty($error)) throw new Exception("Unable to back up page summary and comments");
}

$query = $db->prepare("ALTER TABLE `default_info` DROP COLUMN `summary`");
$query->execute();

$query = $db->prepare("ALTER TABLE `pages` DROP COLUMN `summary`");
$query->execute();

$query = $db->prepare("DROP TABLE `rewidx_comments`");
$query->execute();
