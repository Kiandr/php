<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Property valuation module is enabled, install it's requirements
if (!empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) {
    // DB connection
    $db = DB::get();

    // Output
    echo 'Checking Requirements for CMA Tool:' . PHP_EOL;

    // Make sure cma_location table exists
    $table_name = 'cma_location';
    $checkTable = $db->query("SHOW TABLES LIKE 'cma_location';");
    if ($checkTable->rowCount() > 0) {
        echo PHP_EOL . "\t" . '[✓] Table Exists: ' . $table_name;
    } else {
        // Create cma_location table
        $db->query("CREATE TABLE IF NOT EXISTS `cma_location` ("
            . "`input` varchar(200) NOT NULL,"
            . "`place` varchar(200) NOT NULL,"
            . "`count` int(10) unsigned DEFAULT '1',"
            . "`created` timestamp NULL DEFAULT NULL,"
            . "`updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
            . "PRIMARY KEY (`input`, `place`)"
        . ") ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        // Output
        echo PHP_EOL . "\t" . '[✓] Table Created: ' . $table_name;
    }

    // Group details
    $group_name = Hook_REW_Core_Lead_FormSubmission::SELLER_GROUP_NAME;
    $group_about = 'Leads who have used the CMA tool.';
    $group_style = 'p';

    // Make sure "Seller" group exists - otherwise create it
    $select = $db->prepare("SELECT COUNT(*) FROM `groups` WHERE `name` = :name AND `agent_id` IS NULL AND `associate` IS NULL AND `user` = 'false' LIMIT 1;");
    $select->execute(array('name' => $group_name));
    if ($select->fetchColumn() > 0) {
        echo PHP_EOL . "\t" . '[✓] Group Exists: Seller';
    } else {
        // Create seller group
        $insert = $db->prepare("INSERT INTO `groups` SET `name` = :name, `description` = :description, `style` = :style, `user` = 'false', `timestamp` = NOW();");
        $insert->execute(array('name' => $group_name, 'description' => $group_about, 'style' => $group_style));

        // Output
        echo PHP_EOL . "\t" . '[✓] Created Group: Seller';
    }

    // Make sure snippets exist - otherwise create them
    $snippets = array('cma' => 'module', 'form-cma' => 'form', 'form-cma-capture' => 'form');
    foreach ($snippets as $name => $type) {
        $select = $db->prepare("SELECT COUNT(*) FROM `snippets` WHERE `name` = :name AND `type` = :type AND `agent` IS NULL LIMIT 1;");
        $select->execute(array('name' => $name, 'type' => $type));
        if ($select->fetchColumn() > 0) {
            echo PHP_EOL . "\t" . '[✓] Snippet Exists: #' . $name . '#';
        } else {
            // Locate snippet code
            $snippet_file = __DIR__ . '/_extra/' . $name . '.txt';
            if (!file_exists($snippet_file)) {
                // Error - expected file not found
                throw new Exception('File does not exist: ' . $snippet_file);
            } else {
                // Snippet code
                $code = file_get_contents($snippet_file);

                // Create snippet
                $insert = $db->prepare("INSERT INTO `snippets` SET `name` = :name, `type` = :type, `code` = :code, `agent` = NULL;");
                $insert->execute(array('name' => $name, 'type' => $type, 'code' => $code));

                // Output
                echo PHP_EOL . "\t" . '[✓] Created Snippet: #' . $name . '#';
            }
        }
    }

    // Make sure "cma.php" page exists - otherwise create it
    $select = $db->prepare("SELECT COUNT(*) FROM `pages` WHERE `file_name` = :file_name AND `agent` = 1 LIMIT 1;");
    $select->execute(array('file_name' => 'cma'));
    if ($select->fetchColumn() > 0) {
        echo PHP_EOL . "\t" . '[x] Page Exists: /cma.php';
    } else {
        // Get highest order
        $order = $db->query("SELECT MAX(`category_order`) FROM `pages` WHERE `agent` = 1;");
        $order = $order->fetchColumn();

        // Create cma.php page
        $insert = $db->prepare("INSERT INTO `pages` SET "
            . "`category`		= :category, "
            . "`file_name`		= :file_name, "
            . "`page_title`		= :page_title, "
            . "`link_name`		= :link_name, "
            . "`category_html`	= :category_html, "
            . "`template`		= 'seller', "
            . "`category_order`	= :category_order, "
            . "`hide`			= 'f'"
        . ";");

        // Page details
        $insert->execute(array(
            'file_name'     => 'cma',
            'category'      => 'cma',
            'link_name'     => 'Comparative Market Analysis',
            'page_title'    => 'Comparative Market Analysis',
            'category_html' => implode(PHP_EOL, array(
                '<h1>Receive a free market analysis of comparable properties in your area.</h1>',
                '<p>In today\'s market, it\'s more important than ever to price your home right. We know how to price your property to make sure it sells. If you\'re curious about your home\'s value in today\'s market, just fill out the form below and we\'ll send you a free Comparative Market Analysis.</p>',
                '<p>#cma#</p>',
                '<p><a class="popup" href="/contact.php">Contact us</a> anytime you need to know what\'s really going on in this market. When you\'re ready to take the next step toward selling your home, we\'re here to help. We\'ll make sure your listing gets the best exposure and reaches the right buyer—whether they\'re out of state, in another country, or right around the corner.</p>'
            )),
            'category_order' => $order
        ));

        // Output
        echo PHP_EOL . "\t" . '[✓] Created Page: /cma.php';
    }

    echo PHP_EOL;
}
