<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Property valuation module is enabled, install it's requirements
if (!empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) {
    // DB connection
    $db = DB::get();

    // Make sure snippets exist
    $snippets = array('form-cma' => 'form', 'form-cma-capture' => 'form');
    foreach ($snippets as $name => $type) {
            // Locate snippet code
            $snippet_file = __DIR__ . '/_extra/' . $name . '.txt';

        if (!file_exists($snippet_file)) {
            // Error - expected file not found
            throw new Exception('File does not exist: ' . $snippet_file);
        } else {
            // Snippet code
            $code = file_get_contents($snippet_file);

            $select = $db->prepare("SELECT COUNT(*) FROM `snippets` WHERE `name` = :name AND `type` = :type AND `agent` IS NULL LIMIT 1;");
            $select->execute(array('name' => $name, 'type' => $type));

            if ($select->fetchColumn() == 0) {
                // Create snippet
                $insert = $db->prepare("INSERT INTO `snippets` SET `name` = :name, `type` = :type, `code` = :code, `agent` = NULL;");
                $insert->execute(array('name' => $name, 'type' => $type, 'code' => $code));

                // Output
                echo PHP_EOL . 'Snippet Inserted: #' . $name . '#';
            } else {
                // Create snippet
                $insert = $db->prepare("UPDATE `snippets` SET `code` = :code WHERE `name` = :name AND `type` = :type AND `agent` IS NULL;");
                $insert->execute(array('code' => $code, 'name' => $name, 'type' => $type));

                // Output
                echo PHP_EOL . 'Snippet Updated: #' . $name . '#';
            }
        }
    }
    echo PHP_EOL;
}
