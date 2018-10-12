<?php

// Database Colletion
$DATABASE = array();

// IDX Database Connection
$CONNECTION = array();
$CONNECTION['settings']['type'] = 'MySQLImproved';
$CONNECTION['settings']['host'] = 'idxdb-bareis.gce.rewhosting.com';
$CONNECTION['settings']['user'] = 'idx_bareis';
$CONNECTION['settings']['pass'] = 'H5Wklds3mS1FDg';
$CONNECTION['settings']['db']   = 'rewidx_bareis';

// Add to Database Collection
array_push($DATABASE, array('name' => 'bareis', 'settings' => $CONNECTION['settings']));

// Clear Memory
unset($CONNECTION);
