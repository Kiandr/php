 <?php

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
            if (in_array($id, array('status', 'dom'))) $panel->setFieldType('Select');

            // Remove placeholder from DOM panel
            if (in_array($id, array('dom'))) $panel->setFieldOptions(array('placeholder' => false));

            // Cannot toggle panels
            $panel->setToggle(false);

        }
    }

    // Show advanced options
    $show_advanced = isset($_GET['advanced']) && empty($_GET['refine']);

    // Search search tags
    $idx_tags = $this->config('hideTags') ? [] : IDX_Panel::tags();

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
            'minimum_price',
            'maximum_price',
            'minimum_bedrooms',
            'minimum_bathrooms'
        ) as $var) {
            if (isset($_REQUEST[$var])) continue;
            if (!isset($criteria[$var])) continue;
            $_REQUEST[$var] = $criteria[$var];
        }
    }

}