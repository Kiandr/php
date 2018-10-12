<?php

// Require Composer Vendor Auto loader
require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

// Include BDX Settings
require_once $_SERVER['DOCUMENT_ROOT'] . '/builders/classes/Settings.php';

// Create Settings Class
$settings = Settings::getInstance();

// Get Authorization Managers
$subdomainFactory = Container::getInstance()->get(\REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class);
$subdomain = $subdomainFactory->buildSubdomainFromRequest('canManageBDXSnippets');
if (!$subdomain) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to edit BDX snippets.'
    );
}
$subdomain->validateSettings();

// Success
$success = array();

// Errors
$errors = array();

// Row ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// State List
$states = array();

// Show Form
$show_form = true;

try {
        // BDX Database
        $db_settings = \BDX\Settings::getInstance()->DATABASES['bdx'];
        $db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);

        // CMS Database
        $db_settings = Settings::getInstance()->DATABASES['default'];
        $db = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);

        // Get BDX Settings
        $bdx_settings = \BDX\Settings::getBDXSettings($db);

        // Get state if only 1 enabled
        $state = "";
    if (!empty($bdx_settings['states']) && is_array($bdx_settings['states'])) {
        $enabled_states = array();
        foreach ($bdx_settings['states'] as $key => $val) {
            if ($val['enabled'] == 'true') {
                $enabled_states[$key] = $val;
            }
            if (count($enabled_states) > 1) {
                break;
            }
        }
        if (count($enabled_states) == 1) {
            $state = key($enabled_states);
        }
    } elseif (count(\BDX\Settings::getInstance()->STATES) == 1) {
        $state = \BDX\Settings::getInstance()->STATES[0];
    }

        // Build state list from settings
        $states = \BDX\State::getStateSettings($bdx_settings);

        // Get Search Panels
        $bdx_panels = \BDX\Util::getSearchCriteria($db_bdx, $state, $bdx_settings);

        // Remove Location panel from list
        unset($bdx_panels['Location']);

        // State List
        $statesUSA = \BDX\State::getStates();


        // Select Row
        $result = $db->query("SELECT * FROM `" . TABLE_SNIPPETS . "` WHERE " . $subdomain->getOwnerSql()
        . " AND `name` = " . $db->quote($_GET['id']) . ";");
        $snippet = $result->fetch();

        // Process Submit
    if (isset($_GET['submit'])) {
        // Remember Search Panels (Use next time snippet is added)
        $_SESSION['bdx-snippet-panels'] = !empty($_POST['bdx-panels']) && is_array($_POST['bdx-panels']) ? array_keys($_POST['bdx-panels']) : array();

        // Required Fields
        $required = array();
        $required[] = array('value' => 'snippet_id', 'title' => 'Snippet Name');
        $required[] = array('value' => 'snippet_title', 'title' => 'Snippet Title');

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($_POST[$require['value']])) {
                $errors[] = $require['title'] . ' is a required field.';
            }
        }

        // Page Limit Between 1 and 48
        $page_limit = $_POST['search']['page_limit'];
        if ($page_limit < 1 || $page_limit > 48) {
            $errors[] = 'Page Limit must be a number between 1 and 48.';
        }

        // Generate Snippet
        $code = serialize($_POST);

        // Check Errors
        if (empty($errors)) {
            // Build UPDATE Query
            $query = "UPDATE `" . TABLE_SNIPPETS . "` SET "
                . $subdomain->getAssignSql() . ", "
                . "`name` = " . $db->quote($_POST['snippet_id']) . ", "
                . "`code` = " . $db->quote($code) . ""
                . " WHERE " . $subdomain->getOwnerSql(true) . " AND `name` = '" . $snippet['name'] . "';";

            // Execute Query
            if ($db->query($query)) {
                // Success
                $success[] = 'BDX Snippet has successfully been updated.';

                // Save Notices
                $authuser->setNotices($success, $errors);

                // Redirect to Edit Form
                header('Location: ../edit/?id=' . $_POST['snippet_id']);
                exit;
            } else {
                // Query Error
                $errors[] = 'BDX Snippet could not be created, please try again.';
            }
        }
    }

        // Snippet Criteria
    if (!empty($snippet['code'])) {
        $criteria = unserialize($snippet['code']);
    }

        // Sort Options
        $sort_options = array();

        // Sort Options
        $sort_options['community'] = array(
            array('value' => 'DESC-PriceFrom-Subdivision', 'title' => 'Price, Highest First'),
            array('value' => 'ASC-PriceFrom-Subdivision', 'title' => 'Price, Lowest First'),
            array('value' => 'ASC-SubdivisionName-Subdivision', 'title' => 'Name, Alphabetical Order'),
        );

        $sort_options['home'] = array(
            array('value' => 'DESC-BasePrice-Listing', 'title' => 'Price, Highest First'),
            array('value' => 'ASC-BasePrice-Listing', 'title' => 'Price, Lowest First'),
        );

        // Page Limit
        $_POST['page_limit'] = !empty($_POST['page_limit']) && ($_POST['page_limit'] > 0) ? $_POST['page_limit'] : 6;

        // Default Criteria
    if (!empty($defaults['criteria'])) {
        $criteria = unserialize($defaults['criteria']);
    }

        // Posted Criteria
    if (!empty($code)) {
        $criteria = unserialize($code);
    }

        // Set $_REQUEST Criteria
        $idx = Util_IDX::getIdx();
        $criteria = is_array($criteria) ? $criteria : array();
        $_REQUEST = search_criteria($idx, $criteria);

        // Pages using this Snippet
        $snippet['pages'] = array();

        // Build snippet name like
        $like_name = '%#' . $snippet['name'] . '#%';

        // Check Homepage
        $query = "SELECT `agent` FROM `" . TABLE_SETTINGS . "` WHERE " . $subdomain->getOwnerSql() . " AND `category_html` LIKE " . $db->quote($like_name) . ";";
    if ($result = $db->query($query)) {
        $row = $result->fetch();
        if (!empty($row)) {
            $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/', 'text' => 'Homepage');
        }
    }

        // Locate Pages
        $query = "SELECT `page_id`, `link_name` FROM `" . TABLE_PAGES . "` WHERE " . $subdomain->getOwnerSql() . " AND `category_html` LIKE " . $db->quote($like_name) . " ORDER BY `link_name` ASC;";
    if ($result = $db->query($query)) {
        while ($row = $result->fetch()) {
            $snippet['pages'][] = array('href' => Settings::getInstance()->URLS['URL_BACKEND'] . 'cms/pages/edit/?id=' . $row['page_id'], 'text' => $row['link_name']);
        }
    }
// Error Occurred
} catch (Exception $e) {
        Log::error($e);
}
