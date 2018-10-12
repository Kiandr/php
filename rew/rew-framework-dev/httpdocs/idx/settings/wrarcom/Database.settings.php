<?php

// Database Colletion
$DATABASE = array();

// IDX Database Connection
$CONNECTION = array();
$CONNECTION['settings']['type'] = 'MySQLImproved';
$CONNECTION['settings']['host'] = 'idxdb-wrar.gce.rewhosting.com';
$CONNECTION['settings']['user'] = 'dev_rewtemp_ftp';
$CONNECTION['settings']['pass'] = 'rCg529aO28Utosn0';
$CONNECTION['settings']['db']   = 'rewidx_wrarcom_ftp';

// Add to Database Collection
array_push($DATABASE, array('name' => 'wrarcom', 'settings' => $CONNECTION['settings']));

// Clear Memory
unset($CONNECTION);
