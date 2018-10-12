<?php

use REW\Core\Interfaces\DBInterface;

/* Root Path */
$dir_root = $_SERVER['DOCUMENT_ROOT'];

// Require Composer Vendor Auto loader
require_once $dir_root . '/../boot/app.php';

/* Required Classes */
$dir_classes = Settings::getInstance()->DIRS['CLASSES'];

/* Backend Resources */
$dir_backend = dirname(__FILE__);
require_once $dir_backend . '/inc/lang.en.php';
require_once $dir_backend . '/inc/php/functions/funcs.Template.php';
require_once $dir_backend . '/inc/php/functions/funcs.CMS.php';
require_once $dir_backend . '/inc/php/functions/funcs.ContactSnippets.php';
require_once $dir_backend . '/inc/php/functions/funcs.LeadMan.php';
require_once $dir_backend . '/inc/php/functions/funcs.Blog.php';
if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
    require_once $dir_backend . '/inc/php/functions/funcs.ActionPlan.php';
}

/**
 * Include sql_connect.php
 */
require_once $dir_root . '/sql_connect.php';

// Establish database connection
$db_settings = DB::settings('cms');
@mysql_connect($db_settings['hostname'], $db_settings['username'], $db_settings['password']) or die(Log::halt(mysql_error(), 503));
@mysql_select_db($db_settings['database']) or die(Log::halt(mysql_error(), 503));
mysql_query("SET NAMES 'utf8'");
// Set MySQL Timezone (MySQLi)
mysql_query("SET `time_zone` = '" . $timezone . "';");

/**
 * Include IDX Common File
 */
require_once $dir_root . '/idx/common.inc.php';

/***
 * @TODO: Refine all this old stuff...
 */

    /**
     * Directory Structure
     */
    define('DIR_BACKEND', Settings::getInstance()->DIRS['BACKEND']);
    define('DIR_UPLOADS', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
    define('DIR_AGENT_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/agents/');
    define('DIR_OFFICE_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/offices/');
    define('DIR_SLIDESHOW_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/slideshow/');
    define('DIR_FEATURED_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/featured/');
    define('DIR_LEAD_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/leads/');
    define('DIR_TEAM_IMAGES', $_SERVER['DOCUMENT_ROOT'] . '/uploads/teams/');

    /**
     * HTTP Structure
     */
    define('URL', Settings::getInstance()->SETTINGS['URL']);
    define('URL_BACKEND', Settings::getInstance()->URLS['URL_BACKEND']);
    define('URL_BACKEND_IMAGES', URL_BACKEND . 'img/');
    define('URL_LISTING', URL . 'listing/cms/%s/');
    define('URL_UPLOADS', URL . 'uploads/');
    define('URL_AGENT_IMAGES', URL . 'uploads/agents/');
    define('URL_OFFICE_IMAGES', URL . 'uploads/offices/');
    define('URL_SLIDESHOW_IMAGES', URL . 'uploads/slideshow/');
    define('URL_FEATURED_IMAGES', URL . 'uploads/featured/');
    define('URL_TEAM_IMAGES', URL . 'uploads/teams/');

    /* Agent CMS Site */
if (Settings::getInstance()->SETTINGS['agent'] == 1) {
    define('URL_AGENT_SITE', Http_Uri::getScheme() . '://%s.' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '/');
} else {
    list ($link, $domain) = explode('.', str_replace('www.', '', $_SERVER['HTTP_HOST']), 2);
    define('URL_AGENT_SITE', Http_Uri::getScheme() . '://%s.' . $domain . '/');
}

    // MySQL Tables
    define('TABLE_PAGES', 'pages');
    define('TABLE_REWRITES', 'pages_rewrites');
    define('TABLE_SETTINGS', 'default_info');
    define('TABLE_NUMLINKS', 'numlinks');
    define('TABLE_SNIPPETS', 'snippets');
    define('TABLE_LISTINGS', '_listings');
    define('TABLE_LISTING_FIELDS', '_listing_fields');
    define('TABLE_LISTING_LOCATIONS', '_listing_locations');
    define('TABLE_FEATURED_COMMUNITIES', 'featured_communities');
    define('TABLE_FEATURED_OFFICES', 'featured_offices');
    define('TABLE_FEATURED_LISTINGS', 'featured_listings');
    define('TABLE_SLIDESHOW_IMAGES', 'slideshow_images');
    define('TABLE_TESTIMONIALS', 'testimonials');
    define('LM_TABLE_LEADS', 'users');
    define('LM_TABLE_GROUPS', 'groups');
    define('LM_TABLE_ASSOCIATES', 'associates');
    define('LM_TABLE_LENDERS', 'lenders');
    define('LM_TABLE_USER_GROUPS', 'users_groups');
    define('LM_TABLE_AGENTS', 'agents');
    define('LM_TABLE_NOTES', 'users_notes');
    define('LM_TABLE_MESSAGES', 'users_messages');
    define('LM_TABLE_VISITS', 'users_sessions');
    define('LM_TABLE_PAGEVIEWS', 'users_pageviews');
    define('LM_TABLE_PAGES', 'users_pages');
    define('LM_TABLE_REJECTED', 'users_rejected');
    define('LM_TABLE_SAVED_LISTINGS', 'users_listings');
    define('LM_TABLE_VIEWED_LISTINGS', 'users_viewed_listings');
    define('LM_TABLE_SAVED_SEARCHES', 'users_searches');
    define('LM_TABLE_CAMPAIGNS', 'campaigns');
    define('LM_TABLE_CAMPAIGNS_EMAILS', 'campaigns_emails');
    define('LM_TABLE_CAMPAIGNS_GROUPS', 'campaigns_groups');
    define('LM_TABLE_CAMPAIGNS_USERS', 'campaigns_users');
    define('LM_TABLE_CAMPAIGNS_SENT', 'campaigns_sent');
    define('LM_TABLE_TIMEZONES', 'timezones');
    define('LM_TABLE_DOCS', 'docs');
    define('LM_TABLE_DOC_CATEGORIES', 'docs_categories');
    define('LM_TABLE_DOC_TEMPLATES', 'docs_templates');
    define('CMS_TABLE_FILES', 'cms_files');
    define('CMS_TABLE_UPLOADS', 'cms_uploads');
    define('TABLE_CALENDAR_EVENTS', 'calendar_events');
    define('TABLE_CALENDAR_DATES', 'calendar_dates');
    define('TABLE_CALENDAR_TYPES', 'calendar_types');
    define('TABLE_CALENDAR_REMINDERS', 'calendar_reminders');
    define('TABLE_CALENDAR_ATTENDEES', 'calendar_attendees');
    define('LM_TABLE_REMINDERS', 'users_reminders');
    define('LM_TABLE_DELAYED_EMAILS', 'delayed_emails');
    define('LM_TABLE_FORMS', 'users_forms');
    define('TABLE_LANDING_PODS', 'landing_pods');
    define('TABLE_LANDING_PODS_FIELDS', 'landing_pods_fields');
    define('TABLE_ACTIONPLANS', 'action_plans');
    define('TABLE_TASKS', 'tasks');
    define('TABLE_USERS_ACTIONPLANS', 'users_action_plans');
    define('TABLE_USERS_TASKS', 'users_tasks');
    define('TABLE_USERS_TASKS_NOTES', 'users_tasks_notes');
    define('TABLE_TASKS_EMAILS', 'tasks_emails');
    define('TABLE_TASKS_GROUPS', 'tasks_groups');
    define('TABLE_TASKS_TEXTS', 'tasks_texts');
    define('TABLE_TEAMS', 'teams');
    define('TABLE_TEAM_AGENTS', Settings::getInstance()['TABLES']['LM_TEAM_AGENTS']);
    define('TABLE_TEAM_LISTINGS', Settings::getInstance()['TABLES']['LM_TEAM_AGENT_LISTINGS']);
    define('LM_TABLE_USERS_FIELDS', 'users_fields');
    define('LM_TABLE_USERS_FIELD_STRING', 'users_field_strings');
    define('LM_TABLE_USERS_FIELD_NUMBER', 'users_field_numbers');
    define('LM_TABLE_USERS_FIELD_DATE', 'users_field_dates');

    // Custom Field Indicator
    define('CUSTOM_FIELD_FLAG', 'cst_fld_');

    /* Misc. Settings */
    define('REQUIRED_PAGES', serialize(array('404', 'unsubscribe', 'error')));

    /* REW Blog */
if (!empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/blog/common.inc.php';
}

    /* REW Directory */
if (!empty(Settings::getInstance()->MODULES['REW_DIRECTORY'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/directory/common.inc.php';
}

/**
 *
 */

// Start Session
@session_start();

// Auth User
$authuser = Auth::get();

// Get DB
$db = $app->get(DBInterface::class);

// Validate User
if ($authuser->isValid()) {
    // Set Page Limit
    define('LM_PAGE_LIMIT', $authuser->info('page_limit'));

    // Set Timezone
    date_default_timezone_set($authuser->info('TZ'));
    mysql_query("SET `time_zone` = '" . $authuser->info('TZ') . "';");
    $db->query("SET `time_zone` = '" . $authuser->info('TZ') . "';");
}

// Requested Page
$_GET['page'] = isset($_GET['page']) ? rtrim($_GET['page'], '/') : 'dashboard';

// Reset Password Token
if (strpos($_GET['page'], 'reset/') === 0) {
    list ($_GET['page'], $_GET['token']) = explode('/', $_GET['page']);
}

// Require Authorization
$public = array('login', 'logout', 'remind', 'reset', 'cron', 'server', 'download', 'partners/espresso/api');
if (!in_array($_GET['page'], $public)) {
    if (!$authuser->isValid()) {
        // Remember POST Request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['redirect'] = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') === false ? '?' : '&') . 'restore';
            $_SESSION['post'] = $_POST;
        // Set Redirect URL
        } else {
            $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
        }
        // Redirect to Login Form
        header('Location: ' . URL_BACKEND . 'login/');
        exit;
    }
}

// Over-Ride $_POST With $_GET['post']
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['restore']) && !empty($_SESSION['post']) && is_array($_SESSION['post'])) {
    $_POST = $_SESSION['post'];
    unset($_SESSION['post']);
}

// Action Plans - Task Shortcuts - Load task content into $_POST to pre-populate forms
if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) {
    if (!empty($_GET['post_task'])) {
        if ($task = Backend_Task::load($_GET['post_task'])) {
            $task->postTaskContent();
        }
    }
}
