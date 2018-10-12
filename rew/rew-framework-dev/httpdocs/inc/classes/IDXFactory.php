<?php

use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;

class IDXFactory implements IDXFactoryInterface
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var DBFactoryInterface
     */
    private $dbFactory;

    /**
     * Loaded IDX Objects
     * @var array
     */
    private $feeds = array();

    /**
     * Loaded IDX Databases
     * @var array
     */
    private $dbs = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * IDXFactory constructor.
     * @param SettingsInterface $settings
     * @param DBFactoryInterface $dbFactory
     * @param ContainerInterface $container
     */
    public function __construct(
        SettingsInterface $settings,
        DBFactoryInterface $dbFactory,
        ContainerInterface $container
    ) {
    
        $this->settings = $settings;
        $this->dbFactory = $dbFactory;
        $this->container = $container;
    }

    /**
     * Get IDX Object by Feed Name (Folder Name)
     *
     * @param string $feed
     * @return IDXInterface|null
     * @throws Exception
     */
    public function getIdx($feed = null)
    {
        // Default Feed
        $feed = !empty($feed) ? $feed : $this->settings['IDX_FEED'];

        // No feed to use
        if (empty($feed)) {
            return null;
        }

        // IDX Already Loaded
        if (isset($this->feeds[$feed])) {
            return $this->feeds[$feed];
        }

        $timer = Profile::timer()->stopwatch(__METHOD__)->setDetails('<strong>'.htmlspecialchars($feed).'</strong>')->start();

        // Find IDX Feed
        $feed = str_replace('-', '_', $feed);
        $path = realpath(__DIR__ . '/../../idx/settings/' . $feed);
        if (empty($path)) {
            $feed = str_replace('_', '-', $feed);
            $path = realpath(__DIR__ . '/../../idx/settings/' . $feed);
        }
        if (!empty($path)) {
            // Load Advanced Settings
            $settings = $path . '/Advanced.settings.php';
            if (file_exists($settings)) {
                require $settings;
            }

            // Load IDX Settings
            $settings = $path . '/IDX.settings.php';
            if (!file_exists($settings)) {
                throw new Exception('Missing IDX Feed Settings: ' . $feed);
            }
            require $settings;

            /** @var array|null $IDX */

            // Invalid IDX Settings
            if (!is_array($IDX)) {
                throw new Exception('Missing IDX Feed Settings: ' . $feed);
            }

            // Create IDX Object
            $this->feeds[$feed] = $this->container->make(IDX::class, ['config' => $IDX[0]]);

            // Load Feed Settings
            $this->settings->SETTINGS['map_latitude'] = null;
            $this->settings->SETTINGS['map_longitude'] = null;
            $settings = $path . '/Feed.settings.php';
            if (file_exists($settings)) {
                require $settings;
            }
            $this->feeds[$feed]->setMapCenterpoint([
                'latitude' => $this->settings->SETTINGS['map_latitude'],
                'longitude' => $this->settings->SETTINGS['map_longitude']
            ]);

            // Load Lang Settings
            $settings = $path . '/Lang.settings.php';
            if (file_exists($settings)) {
                require $settings;
            }

            // Load Details Settings
            $settings = $path . '/Details.settings.php';
            if (file_exists($settings)) {
                require $settings;
            }

            // Set details
            if (!empty($_DETAILS)) {
                $this->feeds[$feed]->setDetails($_DETAILS);
            }

            $timer->stop();

            // Return IDX Object
            return $this->feeds[$feed];
        }

        $timer->stop();

        // Unknown IDX Feed
        throw new Exception('Unknown IDX Feed: ' . $feed);
    }

    /**
     * Get IDX Object by Feed Name (Folder Name)
     *
     * @param string $feed
     * @return Database_MySQLImproved|NULL
     * @throws Exception
     */
    public function getDatabase($feed = null)
    {

        // Default Feed
        $feed = !empty($feed) ? $feed : $this->settings['IDX_FEED'];

        // No feed to use
        if (empty($feed)) {
            return null;
        }

        $timer = Profile::timer()->stopwatch(__METHOD__)->setDetails('<strong>'.htmlspecialchars($feed).'</strong>')->start();

        // Database Already Loaded
        if (isset($this->dbs[$feed])) {
            $timer->appendDetails('<br>Database Loaded From Cache')->stop();
            return $this->dbs[$feed];
        }

        // Find IDX Feed
        $feed = str_replace('-', '_', $feed);
        $path = realpath(__DIR__ . '/../../idx/settings/' . $feed);
        if (empty($path)) {
            $feed = str_replace('_', '-', $feed);
            $path = realpath(__DIR__ . '/../../idx/settings/' . $feed);
        }
        if (!empty($path)) {
            // Find Database Settings
            $settings = $path . '/Database.settings.php';
            if (!file_exists($settings)) {
                throw new Exception('Missing IDX Database Settings: ' . $feed);
            }
            require $settings;

            // Invalid Database Settings
            /** @var array|null $DATABASE */
            if (!is_array($DATABASE)) {
                throw new Exception('Missing IDX Database Settings: ' . $feed);
            }

            // Database Settings
            $settings = $DATABASE[0]['settings'];

            // Create Database
            $this->dbs[$feed] = $this->container->make(
                Database_MySQLImproved::class,
                ['host' => $settings['host'], 'user' => $settings['user'], 'pass' => $settings['pass'], 'database' => $settings['db']]
            );

            $timer->stop();

            // Return IDX Database
            return $this->dbs[$feed];
        }

        $timer->stop();

        // Unknown IDX Feed
        throw new Exception('Unknown IDX Feed: ' . $feed);
    }

    /**
     * The method accepts a list of feeds and splits any commingled feeds it finds
     * in to their individual components.  Returns a list of individual feeds that were passed
     * broken up commingled feeds (the commingled feed name itself is removed)
     * @param array $feeds array of feeds
     * @return string array of feeds
     */
    public function parseFeeds($feeds = array())
    {
        foreach ($feeds as $index => $feed) {
            $idx = $this->getIdx($feed);

            if ($idx->isCommingled()) {
                unset($feeds[$index]);
                $commingled_feeds = $idx->getFeeds();

                foreach ($commingled_feeds as $commingledFeed) {
                    $feeds[] = $commingledFeed;
                }
            }
        }

        return $feeds;
    }

    /**
     * Find feed name by settings folder, if feed is supported
     * @param string $slug
     * @return string|NULL
     */
    public function getFeed($slug)
    {

        $timer = Profile::timer()->stopwatch(__METHOD__)->start();

        // Supported IDX Feeds
        $feeds = array('cms' => 'cms');

        // Add configured feeds
        if (!empty($this->settings->IDX_FEEDS)) {
            foreach ($this->settings->IDX_FEEDS as $feed => $settings) {
                $feeds[$feed] = $feed;
            }
        } else {
            $feeds[$this->settings->IDX_FEED] = $this->settings->IDX_FEED;
        }

        // Feed supported?
        $slug = str_replace('-', '_', $slug);
        if (in_array($slug, array_keys($feeds))) {
            $timer->setDetails('Found Supported Feed (Underscore) '.htmlspecialchars($feeds[$slug]))->stop();
            return $feeds[$slug];
        } else {
            $slug = str_replace('_', '-', $slug);
            if (in_array($slug, array_keys($feeds))) {
                $timer->setDetails('Found Supported Feed (Dashes) '.htmlspecialchars($feeds[$slug]))->stop();
                return $feeds[$slug];
            }
        }

        // Not found
        $timer->setDetails('Feed Not Found: '.htmlspecialchars($feeds[$slug]))->stop();
        return null;
    }

    /**
     * Switch to the specified feed & update URLs
     * @param string $slug
     */
    public function switchFeed($slug)
    {

        $timer = Profile::timer()->stopwatch(__METHOD__)->start();

        // Get feed from slug
        $feed = $this->getFeed($slug);

        // Require valid feed
        if (empty($feed)) {
            $timer->setDetails('Invalid Feed Requested '.htmlspecialchars($feed).' from slug '.htmlspecialchars($slug))->stop();
            return;
        }

        // Skip already selected
        if ($feed === $this->settings->IDX_FEED) {
            $timer->setDetails('Already Selected '.htmlspecialchars($feed).' from slug '.htmlspecialchars($slug))->stop();
            return;
        }

        // Remember default
        $this->settings->IDX_FEED_DEFAULT = $this->settings->IDX_FEED;

        // Switch
        $this->settings->IDX_FEED = $feed;

        // HTTP host
        $host = $_SERVER['HTTP_HOST'];

        // Update URLs
        $this->settings->URLS['URL_IDX'] = Http_Uri::getScheme() . '://' . $host. '/idx/' . $slug . '/';
        $this->settings->URLS['URL_IDX_MAP'] = Http_Uri::getScheme() . '://' . $host . '/idx/map/' . $slug . '/';
        $this->settings->SETTINGS['URL_IDX_SEARCH'] = $this->settings->URLS['URL_IDX'] . 'search.html';
        $this->settings->SETTINGS['URL_IDX_REGISTER'] = $this->settings->URLS['URL_IDX'] . 'register.html';
        $this->settings->SETTINGS['URL_IDX_CONNECT'] = $this->settings->URLS['URL_IDX'] . 'connect.html';
        $this->settings->SETTINGS['URL_IDX_DASHBOARD'] = $this->settings->URLS['URL_IDX'] . 'dashboard.html';
        $this->settings->SETTINGS['URL_IDX_LOGIN'] = $this->settings->URLS['URL_IDX'] . 'login.html';
        $this->settings->SETTINGS['URL_IDX_LOGOUT'] = $this->settings->URLS['URL_IDX'] . 'logout.html';
        $this->settings->SETTINGS['URL_IDX_REMIND'] = $this->settings->URLS['URL_IDX'] . 'remind.html';
        $this->settings->SETTINGS['URL_IDX_REGISTER'] = $this->settings->URLS['URL_IDX'] . 'register.html';
        $this->settings->SETTINGS['URL_IDX_SAVED_SEARCH'] = Http_Uri::getScheme() . '://' . $host. '/idx/search/%s/';
        $this->settings->SETTINGS['URL_IDX_VERIFY'] = $this->settings->URLS['URL_IDX'] . 'verify.html?verify=%s';

        $this->loadSettings();

        // Re-bind
        $this->container->set(IDXInterface::class, $this->getIdx());
        $this->container->set(DatabaseInterface::class, $this->getDatabase());

        $timer->setDetails('Switched Feed to '.htmlspecialchars($feed).' from slug '.htmlspecialchars($slug))->stop();
    }

    /**
     * Load IDX Settings
     *
     * @return void
     */
    public function loadSettings()
    {

        $timer = Profile::timer()->stopwatch(__METHOD__)->start();

        // Get Database
        $db = $this->dbFactory->get('cms');

        // REW IDX Settings
        define('REWIDX_TABLE_INFO', '_rewidx_feed');

        // REW IDX Builder Settings
        define('TABLE_IDX_SYSTEM', 'rewidx_system');
        define('TABLE_IDX_DETAILS', 'rewidx_details');
        define('TABLE_IDX_DEFAULTS', 'rewidx_defaults');
        define('TABLE_IDX_SEARCHES', 'rewidx_searches');
        define('TABLE_IDX_QUICKSEARCH', 'rewidx_quicksearch');

        // Load idx settings for current feed
        $system = $db->fetch("SELECT * FROM `" . TABLE_IDX_SYSTEM . "`"
            . " WHERE `idx` IN (" . $db->quote($this->settings->IDX_FEED) . ", '') ORDER BY `idx` DESC LIMIT 1;");

        // Language Settings
        $system['language'] = json_decode($system['language'], true);
        if (!empty($system['language']) && is_array($system['language'])) {
            foreach ($system['language'] as $key => $value) {
                if (isset(Lang::$lang[$key]) && !empty($value)) {
                    Lang::$lang[$key] = $value;
                }
            }
        }

        // Client City List
        global $_CLIENT;
        $_CLIENT['city_list'] = array();
        if (!empty($system['search_cities'])) {
            $cities = explode(',', $system['search_cities']);
            foreach ($cities as $city) {
                $_CLIENT['city_list'][] = array('value' => trim($city), 'title' => ucwords(strtolower(trim($city))));
            }
        }

        // Registration Settings
        if (!empty($system['registration'])) {
            switch ($system['registration']) {
                case 'false':
                    $this->settings->SETTINGS['registration'] = false;
                    $this->settings->SETTINGS['registration_required'] = false;
                    break;
                case 'true':
                    $this->settings->SETTINGS['registration'] = true;
                    $this->settings->SETTINGS['registration_required'] = true;
                    break;
                case 'optional':
                    $this->settings->SETTINGS['registration'] = 'optional';
                    $this->settings->SETTINGS['registration_required'] = false;
                    break;
                default:
                    if (intval($system['registration']) != 0) {
                        // Require Boolean
                        $system['registration_required'] = ($system['registration_required'] == 'true') ? true : false;
                        // Set Settings
                        $this->settings->SETTINGS['registration'] = $system['registration'];
                        $this->settings->SETTINGS['registration_required'] = $system['registration_required'];
                    }
                    break;
            }
        }

        // Require Registrant Password
        $this->settings->SETTINGS['registration_password'] = ($system['registration_password'] == 'true');

        // Require Registrant Phone Number
        $this->settings->SETTINGS['registration_phone'] = ($system['registration_phone'] == 'true');

        // Require Registrant Email Verification
        $this->settings->SETTINGS['registration_verify'] = ($system['registration_verify'] == 'true');

        // Require Registration before viewing all pics
        $this->settings->SETTINGS['registration_on_more_pics'] = false;
        if ($system['registration_on_more_pics'] == 'true') {
            // User Session
            $user = User_Session::get();
            $backend_user = Auth::get();

            if (!$user->isValid() && !$backend_user->isValid()) {
                $this->settings->SETTINGS['registration_on_more_pics'] = true;
            }
        }

        // Force Email Verification (Compliance Requirement)
        if (!empty($_COMPLIANCE['register']['verify'])) {
            $this->settings->SETTINGS['registration_verify'] = true;
        }

        // Default Contact Method
        if (!empty($system['default_contact_method'])) {
            $this->settings->SETTINGS['default_contact_method'] = $system['default_contact_method'];
        }

        // IDX Copy
        if (!empty($system['map_latitude'])) {
            $this->settings->SETTINGS['map_latitude']  = $system['map_latitude'];
        }
        if (!empty($system['map_longitude'])) {
            $this->settings->SETTINGS['map_longitude'] = $system['map_longitude'];
        }
        if (!empty($system['copy_register'])) {
            $this->settings->SETTINGS['copy_register'] = $system['copy_register'];
        }
        if (!empty($system['copy_login'])) {
            $this->settings->SETTINGS['copy_login']    = $system['copy_login'];
        }
        if (!empty($system['copy_connect'])) {
            $this->settings->SETTINGS['copy_connect']  = $system['copy_connect'];
        }

        // Get Details Actions for use in a side nav
        if (in_array($_GET['load_page'], array('details', 'map', 'streetview', 'birdseye', 'local', 'friend', 'inquire'))) {
            // Load details page settings for current feed
            $details = $db->fetch("SELECT * FROM `" . TABLE_IDX_DETAILS . "` WHERE `idx` IN ('" . $this->settings->IDX_FEED . "', '') ORDER BY `idx` DESC LIMIT 1;");

            // Toggle Module Settings
            $this->settings->MODULES['REW_IDX_STREETVIEW']     = !empty($this->settings->MODULES['REW_IDX_STREETVIEW']) && ($details['streetview'] != 'false');
            $this->settings->MODULES['REW_IDX_DIRECTIONS']     = !empty($this->settings->MODULES['REW_IDX_DIRECTIONS']) &&($details['directions'] != 'false');
            $this->settings->MODULES['REW_IDX_BIRDSEYE']       = !empty($this->settings->MODULES['REW_IDX_BIRDSEYE']) &&($details['birdseye'] != 'false');
            $this->settings->MODULES['REW_IDX_SOCIAL_NETWORK'] = !empty($this->settings->MODULES['REW_IDX_SOCIAL_NETWORK']) &&($details['socialnetwork'] != 'false');
            $this->settings->MODULES['REW_IDX_ONBOARD']        = !empty($this->settings->MODULES['REW_IDX_ONBOARD']) &&($details['onboard'] != 'false');
            $this->settings->MODULES['REW_IDX_HISTORY_PRICE']   = !empty($this->settings->MODULES['REW_IDX_HISTORY_PRICE']) &&($details['price_history'] != 'false');
            $this->settings->MODULES['REW_IDX_HISTORY_STATUS']  = !empty($this->settings->MODULES['REW_IDX_HISTORY_STATUS']) &&($details['status_history'] != 'false');
            $this->settings->MODULES['REW_SHOWING_SUITE']   = !empty($this->settings->MODULES['REW_SHOWING_SUITE']) &&($details['showing_suite'] != 'false');
        }

        // Load System Settings
        $columns = "`facebook_apikey`, `facebook_secret`, `google_apikey`, `google_secret`, `microsoft_apikey`, `microsoft_secret`, `linkedin_apikey`, `linkedin_secret`, `twitter_apikey`, `twitter_secret`, `yahoo_apikey`, `yahoo_secret`";
        $settings = $db->fetch("SELECT " . $columns . " FROM `default_info` WHERE `agent` = 1 LIMIT 1;");
        if (!empty($settings)) {
            // Facebook API Settings
            if (!empty($settings['facebook_apikey']) && !empty($settings['facebook_secret'])) {
                $this->settings->SETTINGS['facebook_apikey'] = $settings['facebook_apikey'];
                $this->settings->SETTINGS['facebook_secret'] = $settings['facebook_secret'];
            }

            // Google API Settings
            if (!empty($settings['google_apikey']) && !empty($settings['google_secret'])) {
                $this->settings->SETTINGS['google_apikey'] = $settings['google_apikey'];
                $this->settings->SETTINGS['google_secret'] = $settings['google_secret'];
            }

            // Microsoft API Settings
            if (!empty($settings['microsoft_apikey']) && !empty($settings['microsoft_secret'])) {
                $this->settings->SETTINGS['microsoft_apikey'] = $settings['microsoft_apikey'];
                $this->settings->SETTINGS['microsoft_secret'] = $settings['microsoft_secret'];
            }

            // LinkedIn API Settings
            if (!empty($settings['linkedin_apikey']) && !empty($settings['linkedin_secret'])) {
                $this->settings->SETTINGS['linkedin_apikey'] = $settings['linkedin_apikey'];
                $this->settings->SETTINGS['linkedin_secret'] = $settings['linkedin_secret'];
            }

            // Twitter API Settings
            if (!empty($settings['twitter_apikey']) && !empty($settings['twitter_secret'])) {
                $this->settings->SETTINGS['twitter_apikey'] = $settings['twitter_apikey'];
                $this->settings->SETTINGS['twitter_secret'] = $settings['twitter_secret'];
            }

            // Yahoo API Settings
            if (!empty($settings['yahoo_apikey']) && !empty($settings['yahoo_secret'])) {
                $this->settings->SETTINGS['yahoo_apikey'] = $settings['yahoo_apikey'];
                $this->settings->SETTINGS['yahoo_secret'] = $settings['yahoo_secret'];
            }
        }

        $timer->stop();
    }
}
