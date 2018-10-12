<?php

// Fetch agents
$db = DB::get('users');
$agents = array();
$rows = $db->getCollection('agents')->search()->fetchAll();

// Process results
foreach ($rows as $row) {
    // API object
    $object = new API_Object_Agent($db, $row);

    // Add to collection
    $agents[] = $object->getData();
}

// Set agents
$json = $agents;
