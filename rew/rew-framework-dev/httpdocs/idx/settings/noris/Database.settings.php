<?php

    /* Database Colletion */
    $DATABASE = array();

    /* IDX Database Connection */
    $CONNECTION = array();
    $CONNECTION['settings']['type'] = 'MySQLImproved';
    $CONNECTION['settings']['host'] = 'idxdb-noris.gce.rewhosting.com';
    $CONNECTION['settings']['user'] = 'idx_noris';
    $CONNECTION['settings']['pass'] = 'H5Wklds3mS1FD';
    $CONNECTION['settings']['db']   = 'rewidx_noris';

    /* Add to Database Collection */
    array_push($DATABASE, array('name' => 'noris', 'settings' => $CONNECTION['settings']));

    /* Clear Memory */
    unset($CONNECTION);

?>
