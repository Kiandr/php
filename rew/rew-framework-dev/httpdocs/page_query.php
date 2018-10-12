<?php

// Profile start
$timer_page_query = Profile::timer()->stopwatch('Include <code>/page_query.php</code>')->start();

// Get site settings
$settings = Settings::getInstance();

// HTTP 301 redirect and single page reference recommended for SEO
$missingExtension = isset($_GET['append']);

// Exclude Homepage and pages that are supposed to be .html allowed_extensions
preg_match('/\.(\w+)/', $_GET['id'], $matches);
$extension = $matches[1];
$allowed_extensions = array_keys($settings->FILES['default']['allowed_extensions']);
// Addition extension excluded from redirect
$allowed_extensions[] = 'xml';
$allowed_extensions[] = 'php';
$inExcludedPages = in_array($_GET['id'], ['']) || in_array($extension, $allowed_extensions);

if ($missingExtension && !$inExcludedPages && $settings->HTTP_REDIRECT_PHP) {
    header('Location: /' . $_GET['id']. '.php', true, 301);
    exit;
}

// CMS Database
$db = DB::get('cms');

// CMS Defaults
$defaults = $db->prepare("SELECT * FROM `default_info` WHERE `agent` <=> :agent AND `team` <=> :team;");
$defaults->execute(array('agent' => Settings::getInstance()->SETTINGS['agent'], 'team' => Settings::getInstance()->SETTINGS['team']));
$defaults = $defaults->fetch();

// Agent subdomain
if (isset(Settings::getInstance()->SETTINGS['agent']) && Settings::getInstance()->SETTINGS['agent'] !== 1) {
    $page->info('agent-site', Settings::getInstance()->SETTINGS['agent']);
    $page->info('subdomain-site', true);
} elseif (Settings::getInstance()->SETTINGS['team']) {
    $page->info('team-site', Settings::getInstance()->SETTINGS['team']);
    $page->info('subdomain-site', true);
}

/**
 * Get Compliance details
 */
if ($idx = Util_IDX::getIdx()) {
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($idx->getName());
}

if (!empty($_GET['load_page']) && (!in_array($_GET['load_page'], array('login', 'reset', 'register')))) {
    if (!empty($_COMPLIANCE['pages']) && !empty($_COMPLIANCE['pages'][$_GET['load_page']])) {
        // "Load" MLS compliance page
        $row = $_COMPLIANCE['pages'][$_GET['load_page']];

        if (!isset($row['filename'])) {
            $row['file_name'] = $_GET['load_page'];
            $row['category']  = $_GET['load_page'];
        }
        // Change to a CMS request
        $_GET['id'] = $_GET['load_page'];
        unset($_GET['load_page']);
        $_GET['app'] = 'cms';
    }
}

/**
 * REW IDX
 */
if (isset($_GET['load_page'])) {
    // Require IDX feed to be configured
    $idx_feed = Settings::getInstance()->IDX_FEED;
    if (empty($idx_feed)) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: /');
    }

    // Backwards Compatibility 301s
    if (in_array($_GET['load_page'], array('change_password', 'control_panel', 'favorite_listings', 'messages', 'profile', 'saved_searches'))) {
        $_SESSION['dashboard'] = true;
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: /');
        exit;
    }

    // Clean Page Request
    $_GET['load_page'] = escapeshellcmd($_GET['load_page']);

    // CMS Feed is not an IDX
    if ($idx_feed == 'cms') {
        if (in_array($_GET['load_page'], array('search', 'search_form'))) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /listings.php');
            exit;
        }
    }

    // Define Application
    $page->info('app', ($_GET['load_page'] == 'search_map') ? 'idx-map' : 'idx');

    // Define Page Name
    $page->info('name', str_replace('/', '-', $_GET['load_page']));

    // Load IDX Page
    $row = $page->load('idx', $_GET['load_page'], $idx_feed);

/*
 * REW Blog
 */
} else if (isset($_GET['app']) && $_GET['app'] == 'blog') {
    // Blog Not Installed, Re-Direct to Homepage
    if (empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
        header('Location: /');
        exit;
    }

    // Requested Page
    $_GET['page'] = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'blog';

    // Set Application
    $page->info('app', 'blog');

    // Define Page Name
    $page->info('name', str_replace('/', '-', $_GET['page']));

    // Load Blog Page
    $row = $page->load('blog', $_GET['page']);

/**
 * REW Directory
 */
} else if (isset($_GET['app']) && $_GET['app'] == 'directory') {
    // Directory Not Installed, Re-Direct to Homepage
    if (empty(Settings::getInstance()->MODULES['REW_DIRECTORY'])) {
        header('Location: /');
        exit;
    }

    // Requested Page
    $_GET['page'] = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'directory';

    // Define Application
    $page->info('app', 'directory');

    // Define Page Name
    $page->info('name', str_replace('/', '-', $_GET['page']));

    // Load Directory Page
    $row = $page->load('directory', $_GET['page']);

/**
 * REW Email Viewer
 */
} else if (isset($_GET['app']) && $_GET['app'] == 'email') {
    // CMS Database
    $db = DB::get('cms');

    $_POST['uid'] = isset($_POST['uid']) ? trim($_POST['uid']) : trim($_GET['uid']);

    $event_id = $db->fetch("SELECT event_id FROM users_emails WHERE guid = GuidToBinary(:id);", ["id" => $_POST['uid']]);

    $event = History_Event_Email_Listings::load($event_id["event_id"]);

    if (!empty($event) && is_a($event, "History_Event_Email_Listings"))
        $details = $event->getDetails();

    if (!empty($details)) {
        $start_web_view = strpos($details, 'id="web_view"');
        $end_web_view = strpos($details, "</td>", $start_web_view);
        $web_view = substr($details, $start_web_view, $end_web_view-$start_web_view);
        echo str_replace($web_view, ">", $details);
    } else {
        //Email not found
        header('Location: /404.php');
    }
    exit;
/**
 * Load CMS Content
 */
} else {
    // Define Application
    $page->info('app', 'cms');

    // CMS Page
    if (!empty($_GET['id'])) {
        // Execute page load hook in an attempt to load special pages
        if ($_GET['id'] != '404' && empty($row) && ($loadedPage = Hooks::hook(Hooks::HOOK_SITE_PAGE_LOAD)->run($row, $db, $page, $_GET['id']))) {
            $row = $loadedPage;
        }

        // Load CMS page
        if (empty($row)) {
            $row = $db->prepare("SELECT * FROM `pages` WHERE `agent` <=> :agent AND `team` <=> :team AND BINARY `file_name` = :file_name;");
            $row->execute(array(
                'agent' => Settings::getInstance()->SETTINGS['agent'],
                'team' => Settings::getInstance()->SETTINGS['team'],
                'file_name' => $_GET['id']
            ));
            $row = $row->fetch();
        }

        // Execute page error hook in an attempt to load fallback routes
        if (empty($row) && ($loadedPage = Hooks::hook(Hooks::HOOK_SITE_PAGE_ERROR)->run($row, $db, $page, $_GET['id']))) {
            $row = $loadedPage;
        }

        // CMS Not Found
        if (empty($row)) {
            // 404 Header
            header('HTTP/1.1 404 NOT FOUND');

            // Find 404 Page
            $row = $db->prepare("SELECT * FROM `pages` WHERE `agent` <=> :agent AND `team` <=> :team AND BINARY `file_name` = :file_name;");
            $row->execute(array(
                'agent' => Settings::getInstance()->SETTINGS['agent'],
                'team' => Settings::getInstance()->SETTINGS['team'],
                'file_name' => '404'
            ));
            $row = $row->fetch();
        }

        // Set Page Category
        $page->info('category', $row['category']);

        // Define Page Name
        $page->info('name', $row['file_name']);

        // Open Graph Images
        $og_images = array();
        $query = $db->prepare("SELECT `file` FROM `cms_uploads` WHERE `type` = 'page:og:image' AND `row` = :page_id ORDER BY `order` ASC;");
        $query->execute(array('page_id' => $row['page_id']));
        while ($og_image = $query->fetch()) {
            $og_images[] = Http_Host::getDomainUrl() . 'uploads/' . rawurlencode($og_image['file']);
        }
        $page->info('og:image', $og_images);

        // Set Defaults
        $row['page_title']        = !empty($row['page_title'])        ? $row['page_title']        : $defaults['page_title'];
        $row['meta_tag_desc']     = !empty($row['meta_tag_desc'])     ? $row['meta_tag_desc']     : $defaults['meta_tag_desc'];
        $row['footer']            = !empty($row['footer'])            ? $row['footer']            : $defaults['footer'];

        // Tracking Codes
        $row['uacct']      = $defaults['uacct'];
        $row['verifyv1']   = $defaults['verifyv1'];
        $row['msvalidate'] = $defaults['msvalidate'];
        $row['hittail']    = $defaults['hittail'];

    // CMS Homepage
    } else {
        // Use Defaults
        $row = $defaults;

        // Define Page Name
        $page->info('name', 'homepage');

        // Open Graph Images
        $og_images = array();
        if (!empty(Settings::getInstance()->SETTINGS['team'])) {
            $query = $db->query("SELECT `file` FROM `cms_uploads` WHERE `type` = 'team:og:image' AND `row` = '" . Settings::getInstance()->SETTINGS['team'] . "' ORDER BY `order` ASC;");
        } else if (!empty(Settings::getInstance()->SETTINGS['agent']) && Settings::getInstance()->SETTINGS['agent'] != 1) {
            $query = $db->query("SELECT `file` FROM `cms_uploads` WHERE `type` = 'agent:og:image' AND `row` = '" . Settings::getInstance()->SETTINGS['agent'] . "' ORDER BY `order` ASC;");
        } else {
            $query = $db->query("SELECT `file` FROM `cms_uploads` WHERE `type` = 'default:og:image' ORDER BY `order` ASC;");
        }
        while ($og_image = $query->fetch()) {
            $og_images[] = Http_Host::getDomainUrl() . 'uploads/' . rawurlencode($og_image['file']);
        }
        $page->info('og:image', $og_images);
    }

    // Load Page Template
    $page->loadTemplate($row['template']);

    // Load Page Variables
    $page->loadVariables($row['variables']);

    // Profile start
    $timer_snippets = Profile::timer()->stopwatch('CMS Snippets')->start();

    // Snippet Matches
    preg_match_all("!(#([a-zA-Z0-9_-]+)#)!", $row['category_html'], $matches);
    if (!empty($matches)) {
        // Loop through Snippets
        foreach ($matches[1] as $match) {
            // Load Snippet
            $snippet = $db->prepare("SELECT `name`, `code`, `type` FROM `snippets` WHERE ((`agent` IS NULL AND `team` IS NULL) OR (`agent` <=> :agent AND `team` <=> :team)) AND `name` = :name LIMIT 1;");
            $snippet->execute(array(
                'agent' => (!empty($agent_id) ? $agent_id : Settings::getInstance()->SETTINGS['agent']),
                'team' => (!empty($agent_id) ? null : Settings::getInstance()->SETTINGS['team']),
                'name' => trim($match, '#')
            ));
            $snippet = $snippet->fetch();

            $html = rew_snippet($match, false, null, $snippet);

            // Replace Snippet Contents
            if ($snippet['type'] === 'form') { // Form Snippets, Wrap In <div></div> (To Fix IE9 Issue with .rewfw)
                $row['category_html'] = str_replace($match, '<div>' . $html . '</div>', $row['category_html']);
            } else if ($snippet['type'] === 'idx')  {
                // Over-Ride Page Content if snippet pagination or filter used
                if ((!empty($_REQUEST['p']) && ($_REQUEST['p'] > 1)) || preg_match('/((?:\d+|under|over)-\d+)/', $_REQUEST['price_range'])) {
                    $row['page_title']  = $row['page_title'] . ($_REQUEST['price_range'] ? (' - ' . $_REQUEST['price_range']) : '');
                    $row['category_html'] = $html;
                    break;
                }
                $row['category_html'] = str_replace($match, $html, $row['category_html']);
            } else {
                $row['category_html'] = str_replace($match, $html, $row['category_html']);
            }
        }
    }

    // Profile end
    $timer_snippets->stop();

    // Check Price Range
    if (!empty($_GET['price_range'])) {
        // Invalid Price Range / No IDX Snippet Loaded
        $price_range = get_price_range($_GET['price_range']);
        if (empty($_REQUEST['snippet']) || empty($price_range)) {
            // Send 404 Header
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
        }
    }

    // Used with logo uploader to select the correct logo
    $page->info('template', $row['template']);
}

// MLS Compliance: Strip Words from Meta Description
if (isset($_COMPLIANCE['strip_words']['meta_description']) && is_array($_COMPLIANCE['strip_words']['meta_description'])) {
    foreach ($_COMPLIANCE['strip_words']['meta_description'] as $strip_word) {
        $row['meta_tag_desc'] = str_ireplace($strip_word, '', $row['meta_tag_desc']);
    }
    $row['meta_tag_desc'] = str_ireplace(', ,', '', $row['meta_tag_desc']);
}

// Page Meta Information
$page->info('title', $page->info('title') ?: strip_tags($row['page_title']));
$page->info('meta.description', $page->info('meta.description') ?: strip_tags(str_replace('"', '&quot;', Format::truncate($row['meta_tag_desc'], 1024))));

// Tracking Codes
$page->info('tracking.uacct', trim($defaults['uacct']));
$page->info('tracking.verifyv1', trim($defaults['verifyv1']));
$page->info('tracking.msvalidate', trim($defaults['msvalidate']));
$page->info('tracking.hittail', trim($defaults['hittail']));

// Page Details
$page->info('footer', $row['footer']);
$page->info('features', $row['features']);
$page->info('feature_image', $defaults['feature_image']);

if (array_key_exists('force_register_agent', $_SESSION)) {
    // This is used on skins that load popups inline. They can't receive javascript in a response.
    // Well, not cleanly anyway.
    \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->trackRegisterEvent($_SESSION['force_register_agent'], true);
    unset($_SESSION['force_register_agent']);
}

// Profile end
$timer_page_query->stop();
