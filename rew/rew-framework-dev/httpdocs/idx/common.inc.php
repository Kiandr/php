<?php

// Include SQL Connection & Helper Functions
require_once __DIR__ . '/../sql_connect.php';
require_once __DIR__ . '/../helper_functions.php';

// Profile start
$timer_common = Profile::timer()->stopwatch('Include <code>/idx/common.inc.php</code>')->start();

$timer = Profile::timer()->stopwatch('Resources')->start();

// Require IDX Settings
require_once __DIR__ . '/inc/global.settings.php';
require_once __DIR__ . '/inc/global.functions.php';

// Require IDX Functions
require_once __DIR__ . '/inc/php/functions/snippet.functions.php';
require_once __DIR__ . '/inc/php/functions/template.functions.php';
require_once __DIR__ . '/inc/php/functions/functions.functions.php';

$timer->stop();

// If an agent subdomain and there are no enabled IDXs, then set IDX to CMS
if (Settings::getInstance()->SETTINGS['agent'] != 1 && !\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->hasFrontendIDXAccess()) {
    $_REQUEST['idx'] = 'cms';
}

// CMS Feed
if ($_REQUEST['idx'] == 'cms') {
    Settings::getInstance()->IDX_FEED = 'cms';
}

// Validate URI feed
$uri = Http_Uri::getFullUri();
if (preg_match('/^\/idx\/(map\/)?([a-zA-Z0-9_-]+)\/?$/', $uri, $matches)) {
    $uri_feed = $matches[2];
    $default_feed = !empty(Settings::getInstance()->IDX_FEED_DEFAULT) ? Settings::getInstance()->IDX_FEED_DEFAULT : Settings::getInstance()->IDX_FEED;

    // The default feed for an agent or team subdomain may differ than the main site in a multi-IDX situation
    if (Settings::getInstance()->SETTINGS['agent'] != 1) {
        if (Settings::getInstance()->SETTINGS['team'] && !empty(Settings::getInstance()->SETTINGS['team_idxs']) && is_array(Settings::getInstance()->SETTINGS['team_idxs'])) {
            $default_feed = array_shift(array_values(Settings::getInstance()->SETTINGS['team_idxs']));
        } else if (!empty(Settings::getInstance()->SETTINGS['agent_idxs']) && is_array(Settings::getInstance()->SETTINGS['agent_idxs'])) {
            $default_feed = array_shift(array_values(Settings::getInstance()->SETTINGS['agent_idxs']));
        }
    }

    try {
        if ($uri_feed != 'map') {
            $idx = Util_IDX::getIdx($uri_feed);
            if ($idx->isCommingled()) {
                $commingled_feeds = $idx->getFeeds();
            }
        }
    } catch (Exception $e) {
        Log::error($e);
    }

    // Invalid feed
    if ($uri_feed != 'map'
        && (
            !Util_IDX::getFeed($uri_feed)
            || $uri_feed === $default_feed
            || (// Agent Subdomain Check
                Settings::getInstance()->SETTINGS['agent'] != 1
                && (
                    !in_array($uri_feed, Settings::getInstance()->SETTINGS['agent_idxs'])
                    && (
                        !empty($commingled_feeds)
                        && is_array($commingled_feeds)
                        && array_intersect($commingled_feeds, Settings::getInstance()->SETTINGS['agent_idxs']) == array()
                    )
                )
            )
        )
    ) {
        $url_redirect = $matches[1] === 'map/' ? Settings::getInstance()->URLS['URL_IDX_MAP'] : Settings::getInstance()->SETTINGS['URL_IDX'];
        header('Location: ' . $url_redirect);
        exit;
    }
}

// Redirect Multi-IDX Listing Page Requests That Are Requesting The Default Feed.
$default_feed = !empty(Settings::getInstance()->IDX_FEED_DEFAULT) ? Settings::getInstance()->IDX_FEED_DEFAULT : Settings::getInstance()->IDX_FEED;
if (preg_match('/^\/listing-(' . $default_feed . ')?\//i', $uri, $matches)) {
    $url_redirect = Settings::getInstance()->SETTINGS['URL_RAW'] . str_ireplace('listing-' . $default_feed, 'listing', $uri);
    header('Location: ' . $url_redirect);
    exit;
}


// Switch feed
if (!empty($_REQUEST['feed'])) {
    Util_IDX::switchFeed($_REQUEST['feed']);
} else if (Settings::getInstance()->SETTINGS['agent'] != 1 && \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->hasFrontendIDXAccess()) {
    // Load List Of Available IDX Feeds
    $idx_feeds = !empty(Settings::getInstance()->IDX_FEEDS) ? array_keys(Settings::getInstance()->IDX_FEEDS) : array(Settings::getInstance()->IDX_FEED);

    $parsed_feeds = Util_IDX::parseFeeds($idx_feeds);

	$subdomain_idxs = isset(Settings::getInstance()->SETTINGS['team'])
        ? Settings::getInstance()->SETTINGS['team_idxs']
        : Settings::getInstance()->SETTINGS['agent_idxs'];

	// Set The List Of Agent Feeds To the List Of Feeds That Are Both Available And What The Agent Has Access To
    $subdomain_feeds = array_merge(array_intersect($subdomain_idxs, $parsed_feeds), ['cms']);

    $default_idx = true;
    try {
        $idx = Util_IDX::getIdx(Settings::getInstance()->IDX_FEED);
        if ($idx->isCommingled()) {
            foreach ($subdomain_feeds as $feed) {
                if (!$idx->containsFeed($feed)) {
                    $default_idx = false;
                } else {
                    $default_idx = true;
                    break;
                }
            }
        }
    } catch (Exception $e) {
        Log::error($e);
    }


	// If The Default IDX Is Not Available For The Agent Site
    if (!in_array(Settings::getInstance()->IDX_FEED, $subdomain_feeds) || !$default_idx) {
		// Switch To First Available IDX On The List
        Util_IDX::switchFeed(array_shift($subdomain_feeds));
	}
}

// Global Variables
global $idx, $db_idx, $db_users, $user;

// Get objects
try {
    // IDX & Database
    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();

// Fatal error
} catch (Exception $ex) {
    Log::halt($ex->getMessage());
}

// User Database
$db_settings = DB::settings('users');
$db_users = Container::getInstance()->make(Database_MySQLImproved::class, ['host' => $db_settings['hostname'], 'user' => $db_settings['username'], 'pass' => $db_settings['password'], 'database' => $db_settings['database']]);

// User Session
$user = User_Session::get();

// Auto-Login User from $_GET['uid'] (If not logged into the backend)
if (!empty($_GET['uid'])) {
    $authuser = Auth::get();
    if (empty($authuser) || !$authuser->isValid()) {
        $_GET['uid'] = trim($_GET['uid']);
        if (Validate::guid($_GET['uid'])) {
            $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE `guid` = GuidToBinary('" . $db_users->cleanInput($_GET['uid']) . "') LIMIT 1;");
        } else if (Validate::sha1($_GET['uid'])) {
            $lead = $db_users->fetchQuery("SELECT * FROM `" . TABLE_USERS . "` WHERE SHA1(UPPER(`email`)) = '" . $db_users->cleanInput($_GET['uid']) . "' LIMIT 1;");
        }
        if (!empty($lead)) {
            $user->setUserId($lead['id']);
        }
    }
}

// Validate User
$user->validate();

// Set userTrackID
$userTrackID = $user->user_id() > 0 ? $user->user_id() : false;

// Meta Information
$page_title = Lang::write('IDX_MAIN_PAGE_TITLE');
$meta_desc  = Lang::write('IDX_MAIN_META_DESCRIPTION');
$meta_keyw  = Lang::write('IDX_MAIN_META_KEYWORDS');

// Profile end
$timer_common->stop();
