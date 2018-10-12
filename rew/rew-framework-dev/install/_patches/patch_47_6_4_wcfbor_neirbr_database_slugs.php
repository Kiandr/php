<?php

// Require Composer Vendor Auto loader
require __DIR__ . '/../../vendor/autoload.php';

// DB connection
$db = DB::get();

// Feeds to be changed
$oldFeed = 'wcfbor';
$newFeed = 'neirbr';

// The table name
$tableName = 'snippets';

$tables = array(
    'rewidx_system',
    'rewidx_defaults',
    'rewidx_quicksearch',
    'users_listings',
    'users_listings_dismissed',
    'users_searches',
    'users_viewed_listings',
    'users_viewed_searches'
);

foreach ($tables as $tableName) {

    echo PHP_EOL . 'Replacing ' . $oldFeed . ' with ' . $newFeed . ' in ' . $tableName . PHP_EOL . PHP_EOL;

    // Update old feed names in each table
    $db->query("UPDATE `" . $tableName . "` SET `idx` = '" . $newFeed . "' WHERE `idx` = '" . $oldFeed . "';");

}
