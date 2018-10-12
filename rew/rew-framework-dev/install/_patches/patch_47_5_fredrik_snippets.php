<?php

// Require Composer Vendor Auto loader
require __DIR__ . '/../../boot/app.php';

// Only run for the elite
$skin = Skin::load();
if (!is_a($skin, Skin_FESE::class)) return;

// DB connection
$db = DB::get();

// The snippets to update
$snippets = array(
        'site-footer-broker',
        'site-footer-contact'
);

// The table name
$tableName = 'snippets';

foreach ($snippets as $snippetName) {

    if (file_exists($fileName = __DIR__ . '/../fese/_snippets/' . $snippetName . '.txt') && $fileContents = file_get_contents($fileName)) {
        echo PHP_EOL . 'Inserting ' . $snippetName . ' snippet.' . PHP_EOL . PHP_EOL;

        // Update regardless of previous result. There could be multiple snippets with the same name (i.e. subdomains)
        $db->query("INSERT INTO `" . $tableName . "` SET `agent` = 1, `name` = " . $db->quote($snippetName) . ", `code` = "
            . $db->quote($fileContents) . ", `type` = 'cms' ON DUPLICATE KEY UPDATE `agent` = `agent`");
    }

}
