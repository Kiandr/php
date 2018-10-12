<?php

// Database Colletion
$DATABASE = array();

// IDX Database Connection
$CONNECTION = array();
$CONNECTION['settings']['type'] = 'MySQLImproved';
$CONNECTION['settings']['host'] = 'idxdb-rebgv.gce.rewhosting.com';
$CONNECTION['settings']['user'] = 'dev_rewtemp2';
$CONNECTION['settings']['pass'] = 'rCg529aO28Utosn0';
$CONNECTION['settings']['db']   = 'rewidx_rebgv';

// Add to Database Collection
array_push($DATABASE, array('name' => 'rebgv', 'settings' => $CONNECTION['settings']));

// Clear Memory
unset($CONNECTION);
