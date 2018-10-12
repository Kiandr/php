<?php

// Require Composer Vendor Auto loader
require __DIR__ . '/../../boot/app.php';

// Only run for the elite
$skin = Skin::load();
if (!is_a($skin, Skin_ELITE::class)) return;

// DB connection
$db = DB::get();

$tableName = 'snippets';
$snippetName = 'phone-number-mobile-icon';

if (file_exists($fileName = __DIR__ . '/../elite/_snippets/' . $snippetName . '.txt') && $fileContents = file_get_contents($fileName)) {
    echo PHP_EOL . 'Inserting ' . $snippetName . ' snippet.' . PHP_EOL . PHP_EOL;

    // Update regardless of previous result. There could be multiple snippets with the same name (i.e. subdomains)
    $db->query("INSERT INTO `" . $tableName . "` SET `agent` = 1, `name` = 'phone-number-mobile-icon', `code` = "
        . $db->quote($fileContents) . ", `type` = 'cms' ON DUPLICATE KEY UPDATE `agent` = `agent`");
}