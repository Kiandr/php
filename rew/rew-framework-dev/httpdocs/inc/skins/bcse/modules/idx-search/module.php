<?php

// Ajax search panels
if ($this->config('ajax')) {
    // Search panel list
    $panels = array();

    // IDX property types
    ob_start();
    $panel = IDX_Panel::get('Type', array('toggle' => false));
    $panel->display();
    $panels[] = array('id' => $panel->getId(), 'html' => ob_get_clean());

    // Return JSON response
    header('Content-Type: application/json');
    die(json_encode(array('panels' => $panels)));
}

// Advanced search
if ($this->config('advanced')) {
    // Load default "module.php"
    $controller = $this->locateFile('module.php', __FILE__);
    if (!empty($controller)) {
        require_once $controller;
    }

    // Process panels
    if (!empty($panels)) {
        $skip = array(
            'location',
            'price',
            'type',
            'rooms',
            'bedrooms',
            'bathrooms',
            'polygon',
            'radius',
            'bounds'
        );
        foreach ($panels as $id => $panel) {
            // Skip certain panels
            if (in_array($id, $skip)) {
                unset($panels[$id]);
                continue;
            }

            // Make status & dom panel select inputs
            if (in_array($id, array('status', 'dom'))) {
                $panel->setFieldType('Select');
            }

            // Remove placeholder from DOM panel
            if (in_array($id, array('dom'))) {
                $panel->setFieldOptions(array('placeholder' => false));
            }

            // Cannot toggle panels
            $panel->setToggle(false);
        }
    }

    // Show advanced options
    $settings = $this->getContainer()->getPage()->getSkin()->getSettings();
    $show_advanced = (!empty($settings['more_options']) || isset($_GET['advanced'])) && empty($_GET['refine']);

    // Tags are not set to be hidden
    if ($this->config('hideTags') !== true) {
        // Search search tags
        $idx_tags = IDX_Panel::tags();
    }
} else {
    // DB connection
    $db = DB::get();

    // Load idx defaults for current feed
    $search = $db->fetch("SELECT `criteria` FROM `rewidx_defaults` WHERE `idx` IN (" . $db->quote(Settings::getInstance()->IDX_FEED) . ", '') ORDER BY `idx` DESC LIMIT 1;");

    // Unserialize Search Criteria
    $criteria = unserialize($search['criteria']);
    if (!empty($criteria)) {
        foreach (array(
            'search_location',
            'search_type',
            'minimum_price',
            'maximum_price',
            'minimum_bedrooms',
            'minimum_bathrooms'
        ) as $var) {
            if (isset($_REQUEST[$var])) {
                continue;
            }
            if (!isset($criteria[$var])) {
                continue;
            }
            $_REQUEST[$var] = $criteria[$var];
        }
    }
}
