<?php

// if loaded on homepage reset $_REQUEST, so that IDX snippets don't interfere with the default criteria
$homepage = ($this->getContainer()->getPage()->info('name') == 'homepage' || $_REQUEST['page_request'] == 'homepage');
if ($homepage) {
    $_REQUEST = array();
}

// Advanced search
$displayPanels = array(
    Skin_ELITE::GROUP_PROPERTY_INFO => array(),
    Skin_ELITE::GROUP_PROPERTY_SIZE => array(),
    Skin_ELITE::GROUP_FEATURES => array(),
    Skin_ELITE::GROUP_STATUS => array(),
);
$hiddenPanels = array();
$groupLabels = array(
    Skin_ELITE::GROUP_PROPERTY_INFO => 'Property Info',
    Skin_ELITE::GROUP_PROPERTY_SIZE => 'Property Size',
    Skin_ELITE::GROUP_FEATURES => 'Features & Amenities',
    Skin_ELITE::GROUP_STATUS => 'Property Status',
);

if (empty($_REQUEST['refine']) && empty($_REQUEST['edit_search'])  && empty($_REQUEST['saved_search_id'])) {
    // Always load defaults if not refining
    // DB connection
    $db = DB::get();

    // Load idx defaults for current feed
    $search = $db->fetch("SELECT `criteria` FROM `rewidx_defaults` WHERE `idx` IN (" . $db->quote(Settings::getInstance()->IDX_FEED) . ", '') ORDER BY `idx` DESC LIMIT 1;");

    // Unserialize Search Criteria
    $criteria = unserialize($search['criteria']);
    if (!empty($criteria)) {
        // Set refine, as we will never load defaults again after its been loaded
        $criteria['refine'] = 'true';
        foreach ($criteria as $var => $value) {
            $_REQUEST[$var] = $criteria[$var];
        }
    }
}

if ($this->config('advanced')) {
    // Load default "module.php"
    $controller = $this->locateFile('module.php', __FILE__);
    if (!empty($controller)) {
        require_once $controller;
    }

    // Required panels (displayed in the header
    $missingRequiredPanels = array('location' => true, 'price' => true);

    // Process panels
    if (!empty($panels)) {
        $skip = array(
            'polygon',
            'radius',
            'bounds',
            'location',
        );
        foreach ($panels as $id => $panel) {
            // Skip certain panels
            if (in_array($id, $skip)) {
                unset($panels[$id]);
                continue;
            }
            unset($missingRequiredPanels[$id]);

            // Make status & dom panel select inputs
            if (in_array($id, array('status', 'dom'))) {
                $panel->setFieldType('Select');
            }

            // Remove placeholder from DOM
            if (in_array($id, array('dom'))) {
                $panel->setFieldOptions(array('placeholder' => false));
            }

            // Load the panels we need, sort by group
            $formGroup = $panel->getFormGroup();

            if ($panel->isHidden()) {
                $hiddenPanels[] = $panel;
            } else {
                $displayPanels[$formGroup][] = $panel;
            }
        }
        unset($panels, $panel);

        // Remove empty panel groups
        $displayPanels = array_filter($displayPanels);
    }

    // Append Missing Panels (hidden)
    $missingRequiredPanels = array_filter(array_map(function ($panelName) {
        $panel = IDX_Panel::get($panelName);
        if (!$panel) {
            $error = 'Panel configuration problem. Please contact support.';

            if (!Http_Host::isDev()) {
                // This really should be a fatal error, but if it happens somehow on a live site
                // that would be bad. If we're not in dev, just log it.
                error_log($error);
                return null;
            }
            die(Log::halt($error));
        }
        $panel->setHidden(true);
        return $panel;
    }, array_keys($missingRequiredPanels)));
    $hiddenPanels = array_merge($hiddenPanels, $missingRequiredPanels);

    // Show advanced options
    $settings = $this->getContainer()->getPage()->getSkin()->getSettings();
    $show_advanced = (!empty($settings['more_options']) || isset($_GET['advanced'])) && empty($_GET['refine']);

    // Tags are not set to be hidden
    if ($this->config('hideTags') !== true) {
        // Search search tags
        $idx_tags = IDX_Panel::tags();
    }
}

$app = $this->getContainer()->getPage()->info('app')?:$_GET['app'];
if ($app == 'idx') {
    $page_request = $_GET['page_request']?:strtok(basename($_SERVER['REQUEST_URI']), '?');
    if (!in_array($page_request, array('sitemap.html','search.html'))) {
        $page_request = null;
    }
}
if ($homepage) {
    $page_request = 'homepage';
}

$ajax_url = Settings::getInstance()->SETTINGS['URL_IDX_AJAX'] . 'html.php?app='.$app.'&module=' . $this->getId() . '&page_request=' . $page_request . '&options[advanced]=true&options[template]=ajax.tpl.php';
$current_request = http_build_query(array_diff_key($_REQUEST, array('id' => 1, 'snippet' => 1, 'app' => 1, 'load_page' => 1)));
