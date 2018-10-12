<?php

    // Database Colletion
    $DATABASE = array();

    // IDX Database Connection
    $CONNECTION = array();
    $CONNECTION['settings']['type'] = 'MySQLImproved';
    $CONNECTION['settings']['host'] = 'idxdb-srar.gce.rewhosting.com';
    $CONNECTION['settings']['user'] = 'dev_rewtemp';
    $CONNECTION['settings']['pass'] = 'rCg529aO28Utosn0';
    $CONNECTION['settings']['db']   = 'rewidx_arr';

    // Add to Database Collection
    array_push($DATABASE, array('name' => 'arr', 'settings' => $CONNECTION['settings']));

    // Clear Memory
    unset($CONNECTION);
