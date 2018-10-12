<?php

// Send as Javascript
header('Content-type: application/json');

// Require Backend Common File
$_REQUEST['page'] = 'download';
require_once dirname(__FILE__) . '/../../common.inc.php';

// Output
$files = '[' . PHP_EOL;

// Select CMS Files
$query = "SELECT `id`, `name`, `type`, `size`, `password`, UNIX_TIMESTAMP(`timestamp`) AS `date`"
    . " FROM `" . CMS_TABLE_FILES . "` WHERE "
    . ($authuser->isSuperAdmin() == 'admin' ? "`agent` = 1" : "(`agent` = '" . $authuser->info('id') . "' OR `share` = 'true')")
    . " ORDER BY `timestamp` DESC"
. ";";
if ($result = mysql_query($query)) {
    while ($row = mysql_fetch_assoc($result)) {
        $files .= '{ "title" : "' . $row['name'] . (!empty($row['password']) ? ' (PROTECTED)' : '') . ' [' . Format::filesize($row['size']) . ']", "value" : "/files/' . $row['id'] . '/' . urlencode($row['name']) . '"}, ' . PHP_EOL;
    }
}

echo rtrim($files, ', ' . PHP_EOL) . PHP_EOL;
echo ']' . PHP_EOL;
exit;
