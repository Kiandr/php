<?php

    /* Global Variable Required */
    global $CMS_DB_DATABASE;

    /* Database Colletion */
    $DATABASE = array();

    /* IDX Database Connection */
    $CONNECTION = array();
    $CONNECTION['settings']['type'] = 'MySQLImproved';
    $CONNECTION['settings']['host'] = 'idxdb-nkmls.gce.rewhosting.com';
    $CONNECTION['settings']['user'] = 'idx_nkmls';
    $CONNECTION['settings']['pass'] = 'H5Wklds3mS1FD';
    $CONNECTION['settings']['db']   = 'rewidx_nkmls';

    /* Add to Database Collection */
    array_push($DATABASE, array('name' => 'nkmls', 'settings' => $CONNECTION['settings']));

    /* Clear Memory */
    unset($CONNECTION);

?>
