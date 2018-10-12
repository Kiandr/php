<?php

// Database Colletion
$DATABASE = array();

// Client-specific Database suffix (required for feed compliance)
$client_db_suffix = '##CLIENT##'; // Ex: jsmith

// IDX Database Connection
$CONNECTION = array();
$CONNECTION['settings']['type'] = 'MySQLImproved';
$CONNECTION['settings']['host'] = 'idxdb-sar.gce.rewhosting.com';
$CONNECTION['settings']['user'] = 'dev_rewtemp2';
$CONNECTION['settings']['pass'] = 'rCg529aO28Utosn0';
$CONNECTION['settings']['db']   = 'rewidx_sar_' . $client_db_suffix;

// Add to Database Collection
array_push($DATABASE, array('name' => 'sar', 'settings' => $CONNECTION['settings']));

// Clear Memory
unset($CONNECTION);

// Load CMS Database settings until pointing to correct db.
// Remove this block once set
if ($client_db_suffix == '##CLIENT##') {
	Settings::getInstance()->IDX_FEED = 'cms';
	$path = realpath(__DIR__ . '/../../../idx/settings/cms');
	$settings = $path . '/Database.settings.php';
	if (file_exists($settings)) require $settings;
}
