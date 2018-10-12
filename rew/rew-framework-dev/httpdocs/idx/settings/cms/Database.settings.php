<?php

    // Get DB Settings
    $db_settings = DB::settings('cms');

    /* Database Colletion */
    $DATABASE = array();

    /* User Database Connection */
    $CONNECTION = array();
    $CONNECTION['settings']['type'] = 'MySQLImproved';
    $CONNECTION['settings']['host'] = $db_settings['hostname'];
    $CONNECTION['settings']['user'] = $db_settings['username'];
    $CONNECTION['settings']['pass'] = $db_settings['password'];
    $CONNECTION['settings']['db']   = $db_settings['database'];

    /* Add to Database Collection */
    array_push($DATABASE, array('name' => 'cms', 'settings' => $CONNECTION['settings']));

    /* Clear Memory */
    unset($CONNECTION, $db_settings);

?>