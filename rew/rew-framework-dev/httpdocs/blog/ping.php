<?php

// Require SQL Connect
require_once __DIR__ . '/../sql_connect.php';

// Require Blog Configuration
require_once __DIR__ . '/common.inc.php';

// Blog Not Installed, Re-Directed to Homepage
if (empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    header('Location: /');
    exit;
}

// Require Ping Functions
require_once __DIR__ . '/inc/php/functions/funcs.Ping.php';

// Include XML-RPC Library
require_once Settings::getInstance()->DIRS['LIB'] . 'xmlrpc/xmlrpc.inc.php';
require_once Settings::getInstance()->DIRS['LIB'] . 'xmlrpc/xmlrpcs.inc.php';
require_once Settings::getInstance()->DIRS['LIB'] . 'xmlrpc/xmlrpc_wrappers.inc.php';

// X-Pingback Header
//header('X-Pingback: ' . URL_BLOG . 'ping.php');

// pingback.ping
function pingback_ping($params)
{

    // Collcet Parameters
    $source_url = $params->getParam(0)->scalarval();
    $target_url = $params->getParam(1)->scalarval();

    // Check Fault 0x0010 (16)
    if (url_exists($source_url) != 1) {
        return new xmlrpcresp(0, 16, __('The source URI does not exist.'));
    }

    // Check Fault 0x0020 (32)
    if (url_exists($target_url) != 1) {
        return new xmlrpcresp(0, 32, __('The specified target URI does not exist.'));
    }

    // Check Fault 0x0011 (17)
    if (!url_contains_link($source_url, $target_url)) {
        return new xmlrpcresp(0, 17, __('The source URI does not contain a link to the target URI, and so cannot be used as a source.'));
    }

    // Parse Entry Link from Pingged URL
    $pattern = str_replace("%s", "([A-Za-z0-9_-]+)", str_replace(URL, '', URL_BLOG_ENTRY));
    preg_match("/" . str_replace("/", "\/", $pattern) . "/", $target_url, $entry);

    // DB Connection
    $db = DB::get('blog');

    // Select Entry from Database
    $query = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `link` = :link;");
    $query->execute(array('link' => $entry['1']));
    $entry = $query->fetch();

    // Check Fault 0x0021 (33)
    if (empty($entry)) {
        return new xmlrpcresp(0, 33, __('The specified target URI cannot be used as a target. It either doesn\'t exist, or it is not a pingback-enabled resource.'));
    }

    // Check if Pingback already registered
    $query = $db->prepare("SELECT `id` FROM `" . TABLE_BLOG_PINGS . "` WHERE `agent` = :agent AND `entry` = :entry AND `website` = :website LIMIT 1;");
    $query->execute(array('agent' => $entry['agent'], 'entry' => $entry['id'], 'website' => $source_url));
    $check = $query->fetchColumn();

    // Check Fault 0x0030 (48)
    if (!empty($check)) {
        return new xmlrpcresp(0, 48, __('The pingback has already been registered.'));
    }

    // Select Author from Database
    $query = $db->prepare("SELECT `id`, `email`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `" . TABLE_BLOG_AUTHORS . "` WHERE `id` = :agent;");
    $query->execute(array('agent' => $entry['agent']));
    $author = $query->fetch();

    // Collect Source Meta Information
    $source_info = url_meta_info($source_url, $target_url);

    try {
        // Prepare INSERT Query
        $insert = $db->prepare("INSERT INTO `" . TABLE_BLOG_PINGS . "` SET "
            . "`agent`				= :agent,"
            . "`entry`				= :entry,"
            . "`website`			= :website,"
            . "`excerpt`			= :excerpt,"
            . "`page_title`			= :page_title,"
            . "`meta_tag_keywords`	= :meta_tag_keywords,"
            . "`meta_tag_desc`		= :meta_tag_desc,"
            . "`ip_address`			= :ip_address,"
            . "`timestamp_created`	= NOW()"
        . ";");

        // Execute INSERT Query
        $insert->execute(array(
            'agent'             => $author['id'],
            'entry'             => $entry['id'],
            'website'           => $source_url,
            'excerpt'           => $source_info['excerpt'],
            'page_title'        => $source_info['page_title'],
            'meta_tag_keywords' => $source_info['meta_tag_keywords'],
            'meta_tag_desc'     => $source_info['meta_tag_desc'],
            'ip_address'        => $_SERVER['REMOTE_ADDR']
        ));

        // Create Mailer
        $mailer = new \PHPMailer\RewMailer();
        $mailer->IsHTML(true);

        // Configure Sender
        $mailer->FromName   = Format::htmlspecialchars($author['name']);
        $mailer->From       = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];

        // Add Email Recipient
        $mailer->AddAddress($author['email'], Format::htmlspecialchars($author['name']));

        // Email Subject
        $mailer->Subject = htmlspecialchars_decode('New Blog Pingback - ' . $entry['title']);

        // Email Message (HTML)
        $mailer->Body = '';
        $mailer->Body .= '<p>Your blog entry "<a href="' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '" target="_blank">' . Format::htmlspecialchars($entry['title']) . '</a>" has a new pingback.</p>';
        $mailer->Body .= '<p>You can manage your blog\'s pingback queue from <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . '" target="_blank">' . Settings::getInstance()->URLS['URL_BACKEND'] . '</a>.</p>';
        $mailer->Body .= '<p>';
        $mailer->Body .= '<strong>Entry Title:</strong> ' . Format::htmlspecialchars($entry['title']) . '<br />';
        $mailer->Body .= '<strong>Entry URL:</strong> ' . '<a href="' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '" target="_blank">' . sprintf(URL_BLOG_ENTRY, $entry['link']) . '</a>' . '<br />';
        $mailer->Body .= '</p>';
        $mailer->Body .= '<p>';
        $mailer->Body .= '<strong>Source Title:</strong> ' . Format::htmlspecialchars($source_info['page_title']) . '<br />';
        $mailer->Body .= '<strong>Source URL:</strong> ' . '<a href="' . $source_url . '" target="_blank">' . $source_url . '</a>' . '<br />';
        $mailer->Body .= '<strong>IP Address:</strong> ' . $_SERVER['REMOTE_ADDR'] . '<br />';
        $mailer->Body .= '</p>';

        // Email Message (Pain Text)
        $mailer->AltBody = '';
        $mailer->AltBody .= 'Your blog entry "' . $entry['title'] . '" has a new pingback.' . "\n\n";
        $mailer->AltBody .= 'You can manage your blog\'s pingback queue from ' . Settings::getInstance()->URLS['URL_BACKEND'] . '.' . "\n\n";
        $mailer->AltBody .= 'Entry Title: ' . $entry['title'] . "\n";
        $mailer->AltBody .= 'Entry URL: ' . sprintf(URL_BLOG_ENTRY, $entry['link']) . "\n\n";
        $mailer->AltBody .= 'Source Title: ' . $source_info['page_title'] . "\n";
        $mailer->AltBody .= 'Source URL: ' . $source_url . "\n";
        $mailer->AltBody .= 'IP Address: ' . $_SERVER['REMOTE_ADDR'] . "\n";

        // Send Email
        $mailer->Send();

        // Return Response
        return new xmlrpcresp(new xmlrpcval(__('Pingback has successfully been registered.')));

    // Database error
    } catch (PDOException $e) {
        return new xmlrpcresp(0, 0, __('Pingback could not be registered.'));
    }
}

// pingback services
$services = array(
    'pingback.ping' => array(
        'function' => 'pingback_ping',
        'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString))
    )
);

// create xmlrpc server
if ($_SERVER['REQUEST_METHOD'] != 'POST' || $_SERVER['CONTENT_TYPE'] == 'application/x-www-form-urlencoded') {
    die('XML-RPC server accepts POST requests only.');
} else {
    if (strpos($_SERVER['CONTENT_TYPE'], 'text/xml') === 0) {
        $server = new xmlrpc_server($services, false);
    }
}

// Execute
$server->setdebug(2);
$server->compress_response = true;
$server->service();
