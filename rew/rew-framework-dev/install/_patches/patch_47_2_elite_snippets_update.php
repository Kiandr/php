<?php

// Require Composer Vendor Auto loader
require __DIR__ . '/../../boot/app.php';

// Only run for the elite
$skin = Skin::load();
if (!is_a($skin, Skin_ELITE::class)) return;

// DB connection
$db = DB::get();

// The snippets to update
$snippets = array(
    array(
        'name' => 'form-buyers',
        'legacy_hashes' => array('d3e7f8a9a8dea7dad9f9630380b37478'),
    ),
    array(
        'name' => 'form-seller-radio-simple',
        'legacy_hashes' => array('dc699a7cecb286640fc0a54ccfe0332d'),
    ),
    array(
        'name' => 'form-seller-radio-boxed',
        'legacy_hashes' => array('91470d4c4b4f46bc7009a9eade8106e4'),
    ),
    array(
        'name' => 'navigation',
        'legacy_hashes' => array('d452a46b9f5b40d51169ec2435f6102f'),
    ),
    array(
        'name' => 'mobile-navigation',
        'legacy_hashes' => array('46029688c729c4582214e5dd5ace1bc9'),
    ),
);

// The table name
$tableName = 'snippets';

foreach ($snippets as $snippet) {
    $snippetName = $snippet['name'];
    $snippetLegacyHashes = $snippet['legacy_hashes'];

    // Output
    if (file_exists($fileName = __DIR__ . '/../elite/_snippets/' . $snippetName . '.txt') && $fileContents = file_get_contents($fileName)) {
        echo PHP_EOL . 'Updating ' . $snippetName . ' snippet.' . PHP_EOL . PHP_EOL;

        $snippetHash = md5($fileContents);

        $sqlWhere = array();
        $sqlWhere['shared'] = "`name` = " . $db->quote($snippetName) . " AND MD5(`code`) != " . $db->quote($snippetHash);
        $sqlWhere['md5'] = " MD5(`code`) IN (" . implode(", ", array_map(array($db, 'quote'), $snippetLegacyHashes)) . ")";
        $result = $db->query("SELECT " . $sqlWhere['md5'] . ", `agent` FROM `" . $tableName . "` WHERE " . $sqlWhere['shared']);

        while (list ($md5Check, $agent_id) = $result->fetch(PDO::FETCH_NUM)) {
            if (!$md5Check) {
                echo "\tWARNING: #" . $snippetName . "# (agent #" . $agent_id . ") has been edited. It should be manually updated." . PHP_EOL . PHP_EOL;
            }
        }

        // Update regardless of previous result. There could be multiple snippets with the same name (i.e. subdomains)
        echo "\tUpdated "
            . $db->query("UPDATE `" . $tableName . "` SET `code` = " . $db->quote($fileContents) . " WHERE " . implode(" AND ", $sqlWhere))->rowCount()
            . " rows." . PHP_EOL . PHP_EOL;
    }
}