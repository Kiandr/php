<?php

use REW\Core\Interfaces\Page\BackendInterface;
use REW\Backend\Controller\Navigation\LogoController;
use REW\Backend\Controller\Navigation\HeaderController;
use REW\Backend\Controller\Navigation\SidebarController;

// Output Buffer
ob_start();

// Include Common File
require_once dirname(__FILE__) . '/common.inc.php';

// Set Language
header('Content-Language: ' . Settings::getInstance()->LANG);

// Disable mapping features if no Google API key
$apiKey = Settings::get('google.maps.api_key');
if (empty($apiKey)) {
    Settings::getInstance()->MODULES['REW_IDX_MAPPING']    = false;
    Settings::getInstance()->MODULES['REW_IDX_STREETVIEW'] = false;
    Settings::getInstance()->MODULES['REW_IDX_DIRECTIONS'] = false;
    Settings::getInstance()->MODULES['REW_IDX_ONBOARD']    = false;
}

// Load DIC container instance
$container = Container::getInstance();

// Load current page instance
$page = $container->get(BackendInterface::class);

// Create application instance
$app = $container->make(REW\Backend\Application::class);

// Get Current Page for Timeline
$timelineFactory = $container->get(REW\Backend\Page\TimelineFactory::class);
$pageTimeline = $timelineFactory->build(Settings::getInstance()->URLS['URL_BACKEND'] . $_GET['page'] . '/', array_merge($_GET, $_POST));
$pageTimeline = $pageTimeline->encode();
$timelineMode = $_GET[REW\Backend\Page\Timeline::MODE];

// Include IDX feed switcher on select pages
if (!empty(Settings::getInstance()->IDX_FEEDS) && $authuser->isValid() && in_array($_GET['page'], [
    'cms/tools/communities/add',
    'idx/default-search',
    'idx/quicksearch',
    'idx/searches/add',
    'idx/snippets/add',
    'settings/idx/meta',
    'settings/idx'
])) {
    $page->container('app_sidebar')->module('idx-feeds', [
        'path' => dirname(__FILE__) . '/inc/modules/idx-feeds/'
    ]);
}

// Run app
ob_start();
$app->run($page);
$output = ob_get_clean();
$notifications = $app->getNotices();

// Agent Opt-Out Warning
if ($authuser->isValid() // Is Logged In
    && ($authuser->info('auto_assign_admin') == 'true' || $authuser->info('auto_rotate') == 'true') // Is In Auto-Assignment or Auto-Rotation
    && $authuser->info('auto_assign_agent') == 'false' && $authuser->info('auto_optout') == 'true') { // Is In Auto-Optout and Has Opted Out

    // No Other Messages
    if (empty($warning) && empty($success) && empty($errors)) {
        // Agent Opt-Out Feature
        $optout = new Backend_Agent_OptOut();
        if ($optout->isEnabled()) {
            // Opt-Out From...
            $feature = array();
            if ($authuser->info('auto_assign_admin') == 'true') {
                $feature[] = 'Auto-Assignment';
            }
            if ($authuser->info('auto_rotate') == 'true') {
                $feature[] = 'Auto-Rotation';
            }
            $feature = implode(' and ', $feature);

            // Warning Message
            $notifications->warning('You have been automatically opted-out from ' . $feature . '. You must perform one of the following actions to be opted-in:<ul><li>'
                . implode('</li><li>', array_map(function ($action) use ($optout) {
                    return $optout::$events[$action]['title'];
                }, $optout->getActions()))
            . '</li></ul>');
        }
    }
}

// Get & clear application notices
$notices = $notifications->getAll();
$notifications->clear();

// Define `window.webpackManifest` javascript variable
$webpackManifest = new REW\Backend\Asset\Manifest\ManifestFile(__DIR__ . '/build/js/webpack.json');
$page->addJavascript(sprintf('window.webpackManifest = %s;', json_encode($webpackManifest)), 'global');

// Define `window.__NOTIFICATIONS__` javascript variable
$page->addJavascript(sprintf('window.__NOTIFICATIONS__ = %s;', json_encode($notices)), 'global');

// Define `window.__BACKEND__`
$skinClass = \Skin::getClass();
$page->addJavascript(sprintf('window.__BACKEND__ = %s;', json_encode([
    'tinymce_styles' => $skinClass::getWYSIWYGHelperCSSFile()
])), 'global');

// Require Valid User
if ($authuser->isValid()) {
    // Get Navigation Logo
    $logoNavigationController = $container->get(LogoController::class);
    $logoNavigationController();
    $logoNavigationOutput = ob_get_clean();
    $page->config('app_logo', $logoNavigationOutput);

    // Get Navigation Header
    ob_start();
    $headerNavigationController = $container->get(HeaderController::class);
    $headerNavigationController();
    $headerNavigationOutput = ob_get_clean();
    $page->config('app_header', $headerNavigationOutput);

    // Get Navigation Sidebar
    ob_start();
    $sidebarNavigationController = $container->get(SidebarController::class);
    $sidebarNavigationController();
    $sidebarNavigationOutput = ob_get_clean();
    $page->config('app_sidebar', $sidebarNavigationOutput);
}

// Full Page
if (!isset($_GET['popup']) && !isset($_POST['popup'])) {
    // Overdue Reminders
    $reminders = array();
    if ($authuser->isAgent() || $authuser->isAssociate()) {
        $query = "SELECT `r`.`id`, `r`.`user_id`, `u`.`first_name`, `u`.`last_name`, `u`.`email`, `t`.`title` AS `type`, `r`.`details`, UNIX_TIMESTAMP(`r`.`timestamp`) AS `timestamp`"
               . " FROM `" . LM_TABLE_REMINDERS . "` `r`"
               . " LEFT JOIN `" . LM_TABLE_LEADS . "` `u` ON `r`.`user_id` = `u`.`id`"
               . " LEFT JOIN `" . TABLE_CALENDAR_TYPES . "` `t` ON `r`.`type` = `t`.`id`"
               . " WHERE `r`.`completed` != 'true' AND `r`.`timestamp` < NOW()"
               // Show Agent's Own Reminders (and Shared Reminders)
               . ($authuser->info('mode') == 'agent' ? " AND `u`.`agent` = '" . $authuser->info('id') . "' AND (`r`.`agent` = '" . $authuser->info('id') . "' OR `r`.`share` = 'true')" : '')
               . ($authuser->isAssociate() ? " AND `r`.`associate` = '" . $authuser->info('id') . "'" : '')
               . " ORDER BY `r`.`timestamp` ASC"
               . ";";
        if ($result = mysql_query($query)) {
            while ($row = mysql_fetch_assoc($result)) {
                $row['url'] = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/reminders/?id=' . $row['user_id'];
                $row['name'] = Format::trim($row['first_name'] . ' ' . $row['last_name']);
                $row['name'] = $row['name'] ?: $row['email'];
                $reminders[] = $row;
            }
        }
    }

    // Action Plan Overdue Task Reminders
    if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
        $where   = array();
        $where[] = "`ut`.`status` = 'Pending'";
        $where[] = "`ut`.`timestamp_due` < NOW()";

        $tasks_url = Settings::getInstance()->URLS['URL_BACKEND'];

        if ($authuser->isAgent()) {
            $where[] = "`u`.`agent` = '" . $authuser->info('id') . "'";
            $where[] = "`t`.`performer` = 'Agent'";
            $tasks_url .= 'agents/agent/tasks/';
        } else if ($authuser->isLender()) {
            $where[] = "`u`.`lender` = '" . $authuser->info('id') . "'";
            $where[] = "`t`.`performer` = 'Lender'";
            $tasks_url .= 'lenders/lender/tasks/';
        } else if ($authuser->isAssociate()) {
            $where[] = "`t`.`performer` = 'Associate'";
            $tasks_url .= 'associates/associate/tasks/';
        }
        $tasks_url .= "?id=" . $authuser->info('id');

        // Get overdue tasks for this user's assigned leads
        $query = "SELECT COUNT(`ut`.`task_id`) AS `total` FROM `" . TABLE_TASKS . "` `t` JOIN `" . TABLE_USERS_TASKS . "` `ut` ON `t`.`id` = `ut`.`task_id`"
            . " JOIN `" . LM_TABLE_LEADS . "` `u` ON `ut`.`user_id` = `u`.`id` WHERE " . implode(' AND ', $where) . ";";
        if ($result = mysql_query($query)) {
            $overdue_tasks = mysql_fetch_assoc($result);
            if ($overdue_tasks['total'] > 0) {
                $reminders[] = array(
                    'action_plan'   => true,
                    'tasks_url'     => $tasks_url,
                    'overdue_tasks' => $overdue_tasks['total']
                 );
            }
        }
    }
}

// AJAX Request
if (!empty($_POST['ajax'])) {
    // Send as JSON
    header("Content-type: application/json");

    // JSON
    $json = array();

    // Page Content
    $json['content'] = $output;

    // Encode JSON
    $json = json_encode($json);

    // Return JSON
    die($json);
}

// Create Default Page Title
if (!isset($page_subtitle)) {
    $page_subtitle = [];
    $titles = explode('/', $_GET['page']);
    for ($i = 0; $i < count($titles); $i++) {
        $current_title = implode(' ', array_map(function ($current) {
            if (in_array($current, ['cms','idx','bdx','rt'])) {
                return strtoupper($current);
            } else {
                return ucfirst(strtolower($current));
            }
        }, preg_split("/[\s,-]+/", $titles[$i])));
        if (in_array($current_title, ['Add','Edit', 'Delete'])) {
            $title_mode = $current_title;
        } else {
            $page_subtitle []= $current_title;
        }
    }
    if (isset($title_mode)) {
        array_unshift($page_subtitle, $title_mode);
    }
    $page_subtitle = implode(' ', $page_subtitle);
}


/* Flyout Section */
$display_flyout = false;
if ($authuser->isAgent()) {
    $display_flyout = true;
}
$page->info('display_flyout', $display_flyout);

// Set Page Content
$page->config('content', $output);

/** @var Skin_Backend $skin */
$skin = $page->getSkin();

// Set Body Class
$skin->setBodyClass($body_class);

// Display Page
$page->display();

//if (Settings::isREW()) Log::display();
