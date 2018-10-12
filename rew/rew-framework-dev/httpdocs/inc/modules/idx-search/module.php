<?php

// CMS Database
$db = DB::get('cms');

// Search Mode (refine|quicksearch)
$mode = $this->config('mode') ? $this->config('mode') : 'refine';

// Button Text (string)
$button = $this->config('button') ? $this->config('button') : Lang::write('IDX_SEARCH_REFINE_BUTTON');
;

// Panels Supplied
$panels = $this->config('panels');

// Use IDX Defaults
$defaults = $this->config('defaults');
$defaults = isset($defaults) ? $defaults : true;

// Hide IDX Tags
$this->config('hideTags', !empty($_REQUEST['hide_tags']));

// Agent, Create / Edit Saved Search
$backend_user = Auth::get();
$backend_user = $backend_user->isValid() ? $backend_user : false;
if (!empty($backend_user) && !empty($_REQUEST['lead_id'])) {
    $lead = $db->fetch("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `id` = " . $db->quote($_REQUEST['lead_id']) . ";");
}

// Load IDX Panels
if (empty($panels)) {
    // Custom Search
    if (!empty($_REQUEST['search_id'])) {
        $search = $db->fetch("SELECT * FROM `rewidx_searches` WHERE MD5(`id`) = " . $db->quote($_REQUEST['search_id']) . ";");

    // Load Quick Search
    } else if ($mode == 'quicksearch') {
        $search_query = $db->prepare("SELECT *, `search_panels` AS `panels` FROM `rewidx_quicksearch` WHERE `agent` <=> :agent AND `team` <=> :team AND `idx` = :idx;");
        $search_query->execute([
            'agent' => Settings::getInstance()->SETTINGS['agent'],
            'team' => Settings::getInstance()->SETTINGS['team'],
            'idx' => Settings::getInstance()->IDX_FEED
        ]);
        $search = $search_query->fetch();
        if (empty($search)) {
            $search_query = $db->prepare("SELECT *, `search_panels` AS `panels` FROM `rewidx_quicksearch` WHERE `agent` <=> :agent AND `team` <=> :team AND `idx` = '';");
            $search_query->execute([
                'agent' => Settings::getInstance()->SETTINGS['agent'],
                'team' => Settings::getInstance()->SETTINGS['team']
            ]);
            $search = $search_query->fetch();
        }
    } else {
        // Load Saved Search
        if (!empty($_REQUEST['saved_search_id'])) {
            $saved_search = $db->fetch("SELECT `id`, `idx`, `title`, `frequency` FROM `users_searches` WHERE `id` = " . $db->quote($_REQUEST['saved_search_id']) . ";");

            if ($saved_search['idx'] != Settings::getInstance()->IDX_FEED) {
                Util_IDX::switchFeed($saved_search['idx']);
            }
        }

        // Load idx defaults for current feed
        $search = $db->fetch("SELECT * FROM `rewidx_defaults` WHERE `idx` IN (" . $db->quote(Settings::getInstance()->IDX_FEED) . ", '') ORDER BY `idx` DESC LIMIT 1;");
    }

    // Search Panels
    if (!empty($search['panels'])) {
        $panels = unserialize($search['panels']);
    }

    // Search Panels
    if (empty($panels)) {
        // Default Panels
        $panels = IDX_Panel::defaults();
    }

    // Search Split
    if (!empty($search['split'])) {
        $before = $panels;
    }

    // Append Missing Panels
    IDX_Panel::displayMissing($panels, $_REQUEST);

    // Force Map Panels on Map Search
    if ($_GET['load_page'] == 'search_map') {
        $panels = array_merge_recursive(array(
            'polygon' => array('display' => true),
            'radius'  => array('display' => true),
            'bounds'  => array('display' => true)
        ), $panels);
    } else {
        // Set Panels as Hidden
        if (!empty($panels['polygon'])) {
            $panels['polygon']['hidden'] = true;
        }
        if (!empty($panels['radius'])) {
            $panels['radius']['hidden'] = true;
        }
        if (!empty($panels['bounds'])) {
            $panels['bounds']['hidden'] = true;
        }
    }

    // Update Split
    if (!empty($search['split'])) {
        $after = array_diff_assoc($panels, $before);
        $search['split'] += count($after);
    }
} elseif (!empty($defaults)) {
    // Load idx defaults for current feed
    $search = $db->fetch("SELECT `criteria` FROM `rewidx_defaults` WHERE `idx` IN (" . $db->quote(Settings::getInstance()->IDX_FEED) . ", '') ORDER BY `idx` DESC LIMIT 1;");

    // Unserialize Search Criteria
    $criteria = unserialize($search['criteria']);
    if (!empty($criteria)) {
        $_REQUEST = array_merge($criteria, $_REQUEST);
    }
}

// Load Panels
foreach ($panels as $id => $panel) {
    // IDX_Panel, Use As-Is.
    if ($panel instanceof IDX_Panel) {
        continue;
    }

    // Panel Options
    $open   = $panel['display'];
    $closed = $panel['collapsed'];
    $hidden = $panel['hidden'];

    // Load IDX Panel
    $panel = !empty($open) || !empty($hidden) ? IDX_Panel::get($id, array_merge($panel, array(
        'closed' => !empty($closed) ? true : false,
        'hidden' => !empty($hidden) ? true : false,
        'fieldType' => $panel['fieldType'] // @see IDX_Panel_Type::__construct
    ))) : false;

    // Require Panel
    if (!empty($panel)) {
        $panels[$id] = $panel;
    } else {
        unset($panels[$id]);
    }
}
