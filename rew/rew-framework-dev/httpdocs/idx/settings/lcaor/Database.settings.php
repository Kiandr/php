<?php

// Database Colletion
$DATABASE = array();

// IDX Database Connection
$CONNECTION = array();
$CONNECTION['settings']['type'] = 'MySQLImproved';
$CONNECTION['settings']['host'] = 'idxdb-lcaor.gce.rewhosting.com';
$CONNECTION['settings']['user'] = 'dev_rewtemp';
$CONNECTION['settings']['pass'] = 'rCg529aO28Utosn0';
$CONNECTION['settings']['db']   = 'rewidx_lcaor';

// Add to Database Collection
array_push($DATABASE, array('name' => 'lcaor', 'settings' => $CONNECTION['settings']));

// Clear Memory
unset($CONNECTION);
