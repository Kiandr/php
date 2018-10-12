<?php

// Database Colletion
$DATABASE = array();

// IDX Database Connection
$CONNECTION = array();
$CONNECTION['settings']['type'] = 'MySQLImproved';
$CONNECTION['settings']['host'] = 'idxdb-uat-01.gce.rewhosting.com';
$CONNECTION['settings']['user'] = 'proddev_1';
$CONNECTION['settings']['pass'] = 'Wt9dY8RKxONwDC8L';
$CONNECTION['settings']['db']   = 'rewidx_uat_2';

// Add to Database Collection
array_push($DATABASE, array('name' => 'uat2', 'settings' => $CONNECTION['settings']));

// Clear Memory
unset($CONNECTION);
