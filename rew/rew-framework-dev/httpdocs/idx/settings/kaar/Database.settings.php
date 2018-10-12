<?php

    // Database Colletion
    $DATABASE = array();

    // IDX Database Connection
    $CONNECTION = array();
    $CONNECTION['settings']['type'] = 'MySQLImproved';
    $CONNECTION['settings']['host'] = 'idxdb-kaar.gce.rewhosting.com';
    $CONNECTION['settings']['user'] = 'dev_rewtemp';
    $CONNECTION['settings']['pass'] = 'rCg529aO28Utosn0';
    $CONNECTION['settings']['db']   = 'rewidx_kaar';

    // Add to Database Collection
    array_push($DATABASE, array('name' => 'kaar', 'settings' => $CONNECTION['settings']));

    // Clear Memory
    unset($CONNECTION);

?>
