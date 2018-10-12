<?php

// Include Common File
require_once '../../../common.inc.php';

try {
    // Get Database
    $db = DB::get('users');

    // Load Document from Database
    $document = $db->prepare("SELECT * FROM `" . LM_TABLE_DOCS . "` WHERE `id` = :id;");
    $document->execute(array('id' => $_GET['id']));
    $document = $document->fetch();
    if (empty($document)) {
        throw new Exception('Document not found.');
    }

    // Success, Return Document
    $json = array_merge(array(
        'returnCode' => 200
    ), $document);

// Database error
} catch (PDOException $e) {
    $json['message'] = 'Error';

// Error occurred
} catch (Exception $e) {
    $json['message'] = $e->getMessage();
}

// JSON Response
header('Content-type: application/json');
die(json_encode($json));
