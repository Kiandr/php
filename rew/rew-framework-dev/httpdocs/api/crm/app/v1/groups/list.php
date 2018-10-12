<?php

// Fetch groups
$db = DB::get('users');
$groups = array();

// Search where
$where = array();

// Agent filter
if (!empty($_GET['agent_id'])) {
    $where = array(
        '$eq' => array(
            'agent_id' => intval($_GET['agent_id']),
        ),
    );
}

// Fetch collection
$rows = $db->getCollection('groups')->search($where)->fetchAll();

// Process results
foreach ($rows as $row) {
    // API object
    $object = new API_Object_Group($db, $row);

    // Add to collection
    $groups[] = $object->getData();
}

// Set groups
$json = $groups;
