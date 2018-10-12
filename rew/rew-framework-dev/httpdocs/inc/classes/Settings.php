<?php

use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Http\HostInterface;
use REW\Core\Interfaces\EnvironmentInterface;
use REW\Core\Interfaces\SettingsFileMergerInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;

/**
 * Settings - Application level settings
 *
 */
class Settings implements SettingsInterface
{

    /**
     * Track executed build methods to ensure we only run each once
     * @var array
     */
    private $_executedBuildMethods = array();

    /**
     * Configuration Settings
     * @var array
     */
    private $config = array(
        'app_name'              => 'REW CRM',
        'app_version'           => 3.2,
        'app_build'             => '0-dev',
        'tables' => array(
            'HISTORY_EVENTS'                  => 'history_events',
            'HISTORY_USERS'                   => 'history_users',
            'HISTORY_DATA'                    => 'history_data',
            'HISTORY_DATA_NORMAL'             => 'history_data_normal',
            'LM_ACTION_PLANS'                 => 'action_plans',
            'LM_AGENTS'                       => 'agents',
            'LM_AGENTS_SOCIAL_NETWORKS'       => 'agents_social_networks',
            'LM_ASSOCIATES'                   => 'associates',
            'LM_AUTH'                         => 'auth',
            'LM_EVENT_DISMISSED'              => 'dashboard_dismissed',
            'LM_OFFICES'                      => 'featured_offices',
            'LM_GROUPS'                       => 'groups',
            'LM_TASKS'                        => 'tasks',
            'LM_TASK_EMAILS'                  => 'tasks_emails',
            'LM_TASK_GROUPS'                  => 'tasks_groups',
            'LM_TASK_TEXTS'                   => 'tasks_texts',
            'LM_USER_FORMS'                   => 'users_forms',
            'LM_USER_LISTINGS'                => 'users_listings',
            'LM_USER_MESSAGES'                => 'users_messages',
            'LM_USER_NOTES'                   => 'users_notes',
            'LM_USER_TASKS'                   => 'users_tasks',
            'LM_USER_TASK_NOTES'              => 'users_tasks_notes',
            'LM_USER_SEARCHES'                => 'users_searches',
            'LM_USER_VIEWED_LISTINGS'         => 'users_viewed_listings',
            'LM_TEAMS'                        => 'teams',
            'LM_TEAM_AGENTS'                  => 'team_agents',
            'LM_TEAM_AGENT_LISTINGS'          => 'team_agent_listings',
            'LM_LEADS'                        => 'users',
            'LM_LENDERS'                      => 'lenders',
            'LM_TRANSACTIONS'                 => 'users_transactions',
            'LM_TIMEZONES'                    => 'timezones',
            'LM_LOCATIONS'                    => '_listing_locations',
            'LM_USER_GROUPS'                  => 'users_groups',
            'LM_VISITS'                       => 'users_sessions',
            'LM_USER_ACTIONPLANS'             => 'users_action_plans',
            'LM_CAMPAIGNS'                    => 'campaigns',
            'LM_CAMPAIGNS_EMAILS'             => 'campaigns_emails',
            'DOCUMENTS'                       => 'docs',
            'DOCUMENTS_CATEGORIES'            => 'docs_categories',
            'UPLOADS'                         => 'cms_uploads',
            'PAGES'                           => 'pages',
            'SNIPPETS'                        => 'snippets',
            'DEFAULT_INFO'                    => 'default_info',
            'NUMLINKS'                        => 'numlinks',
            'IDX_QUICKSEARCH'                 => 'rewidx_quicksearch',
            'IDX_SYSTEM'                      => 'rewidx_system',
            'SETTINGS'                        => 'settings',
            'TIMELINE_PAGES'                  => 'timeline_pages',
            'TIMELINE_PAGE_VARIABLES'         => 'timeline_page_variables',
        ),
        'urls' => array(
            'URL'                             => '%host%/',
            'URL_DOMAIN'                      => '%scheme%://%domain%/',
            'URL_SUBDOMAIN'                   => '%scheme%://%s.%domain%/',
            'URL_BLOG'                        => '%host%/blog/',
            'URL_BACKEND'                     => '%root%/backend/',
            'URL_BACKEND_AJAX'                => '%root%/backend/inc/php/ajax/',
            'URL_IDX'                         => '%host%/idx/',
            'URL_IDX_MAP'                     => '%host%/idx/map/',
            'URL_AGENT'                       => '%host%/agents/%s/',
            'URL_OFFICE'                      => '%host%/offices.php?oid=%d',
            'URL_RT'                          => '%host%/rt/',
            'CACHE'                           => '%root%/inc/cache/',
            'UPLOADS'                         => '%root%/uploads/',
            'UPLOADS_OFFICE'                  => '%root%/uploads/offices/',
            'BACKEND_LIB'                     => '%host%/backend/inc/lib/',
            'HONEYPOT'                        => 'inc/skins/default/tpl/partials/honeypot.tpl'
        ),
        'dirs' => array(
            'ROOT'                            => '%root%/',
           'INSTALL'                          => '%root%/../install/',
            'CACHE'                           => '%root%/inc/cache/',
            'UPLOADS'                         => '%root%/uploads/',
            'UPLOADS_OFFICE'                  => '%root%/uploads/offices/',
            'UPLOADS_LEADS'                   => '%root%/uploads/leads/',
            'CLASSES'                         => '%root%/inc/classes/',
            'SKINS'                           => '%root%/inc/skins/',
            'LIB'                             => '%root%/inc/lib/',
            'LANG'                            => '%root%/inc/lang/',
            'BACKEND'                         => '%root%/backend/',
            'BACKEND_LIB'                     => '%root%/backend/inc/lib/',
            'RT'                              => '%root%/../vendor/rew-modules/realtytrac/classes/Views/Framework/',
            'RT_MODULES'                      => '%root%/../vendor/rew-modules/realtytrac/modules/',
        ),
        'settings' => array(
            /**
             * Agent CMS Settings
             */
            'agent'                  => 1,
            'agent_idxs'             => array(),

            /**
             * Agent CMS Settings
             */
            'team'                   => null,
            'team_idxs'              => array(),

            /**
             * Site Compression
             */
            'MINIFY_JS'              => true,
            'MINIFY_CSS'             => true,
            'MINIFY_HTML'            => true,

            /**
             * Registration Settings
             */
            'registration'              => false,
            'registration_required'     => false,
            'registration_on_more_pics' => false,
            'registration_password'     => false,
            'registration_phone'        => false,
            'registration_verify'       => false,
            'default_contact_method'    => 'email',

            /**
             * Map Settings
             */
            'map_latitude'      => 0,
            'map_longitude'     => 0,
            'map_zoom'          => 12,
            'map_state'         => '',

            /**
             * URL Structure
             * @see __construct
             */
            'URL'                    => '',
            'URL_IDX'                => '',
            'URL_IMG'                => '',
            'URL_IDX_AJAX'           => '',
            'URL_IDX_DASHBOARD'      => '',
            'URL_IDX_LOGIN'          => '',
            'URL_IDX_LOGOUT'         => '',
            'URL_IDX_REMIND'         => '',
            'URL_IDX_CONNECT'        => '',
            'URL_IDX_REGISTER'       => '',
            'URL_IDX_SEARCH'         => '',
            'URL_IDX_SAVED_SEARCH'   => '',
            'URL_IDX_VERIFY'         => '',
            'URL_AGENT_SITE'         => '',
            'URL_RT'                => '',

            /**
             * Image Settings
             */
            'img_quality'           => 85,
            'img_max_height'        => 2000,
            'img_max_width'         => 2000,
        )
    );

    /**
     * DB Connection
     * @var string|DB
     */
    private $_db;

    /**
     * SQL Query to Load Setting
     * @var string|PDOStatement
     */
    private $_db_stmt_select = 'SELECT `value` FROM `settings` WHERE `name` = :name LIMIT 1;';

    /**
     * SQL Query to Save Setting
     * @var string|PDOStatement
     */
    private $_db_stmt_insert = 'INSERT INTO `settings` SET `name` = :name, `value` = :value ON DUPLICATE KEY UPDATE `value` = :value;';

    /**
     * Settings from DB
     * @var array
     */
    private $_settings = array();

    /**
     * @var DBFactoryInterface
     */
    private $_dbFactory;

    /**
     * @var HostInterface
     */
    private $httpHost;

    /**
     * Settings constructor.
     * @param SettingsFileMergerInterface $merger
     * @param ContainerInterface $container
     * @param DBFactoryInterface $dbFactory
     * @param HostInterface $httpHost
     */
    public function __construct(
        SettingsFileMergerInterface $merger,
        ContainerInterface $container,
        DBFactoryInterface $dbFactory,
        HostInterface $httpHost
    ) {
        $this->config = $merger->recursiveMerge(
            $this->config,
            $merger->importAndMergeSources($container->get(static::CONFIG_IMPORTS_KEY))
        );
        $this->_dbFactory = $dbFactory;
        $this->httpHost = $httpHost;

        // Set local
        setlocale(LC_CTYPE, str_replace('-', '_', $this->config['lang']).'.utf8');

        // Application Revision
        $this->config['app_revision'] = $this->config['app_version'] . '.' . $this->config['app_build'];

        // HTTP Information
        $http_host = $_SERVER['HTTP_HOST'];
        $http_domain = $this->httpHost->getDomain();
        $http_scheme = !empty($this->config['ssl']) && $this->config['ssl'] === true ? 'https' : 'http';

        // Set Dynamic Properties
        $this->config['settings']['URL']                  = $http_scheme . '://' . $http_host . '/';
        $this->config['settings']['URL_RAW']              = $http_scheme . '://' . $http_host;
        $this->config['settings']['URL_IDX']              = $http_scheme . '://' . $http_host . '/idx/';
        $this->config['settings']['URL_IMG']              = $http_scheme . '://' . $http_host . '/img/';
        $this->config['settings']['URL_IDX_AJAX']         = $this->config['settings']['URL']     . 'idx/inc/php/ajax/';
        $this->config['settings']['URL_IDX_DASHBOARD']    = $this->config['settings']['URL_IDX'] . 'dashboard.html';
        $this->config['settings']['URL_IDX_LOGIN']        = $this->config['settings']['URL_IDX'] . 'login.html';
        $this->config['settings']['URL_IDX_LOGOUT']       = $this->config['settings']['URL_IDX'] . 'logout.html';
        $this->config['settings']['URL_IDX_REMIND']       = $this->config['settings']['URL_IDX'] . 'remind.html';
        $this->config['settings']['URL_IDX_REGISTER']     = $this->config['settings']['URL_IDX'] . 'register.html';
        $this->config['settings']['URL_IDX_CONNECT']      = $this->config['settings']['URL_IDX'] . 'connect.html';
        $this->config['settings']['URL_IDX_SEARCH']       = $this->config['settings']['URL_IDX'] . 'search.html';
        $this->config['settings']['URL_IDX_SAVED_SEARCH'] = $this->config['settings']['URL_IDX'] . 'search/%s/';
        $this->config['settings']['URL_IDX_VERIFY']       = $this->config['settings']['URL_IDX'] . 'verify.html?verify=%s';
        $this->config['settings']['URL_IDX_NEWSLETTER']   = $this->config['settings']['URL_IDX'] . 'newsletter.html';
        $this->config['settings']['URL_AGENT_SITE']       = $http_scheme . '://%s.' . str_replace('www.', '', $http_host) . '/';
        $this->config['settings']['URL_AGENT_SITE_RAW']   = $http_scheme . '://%s.' . str_replace('www.', '', $http_host);
        $this->config['settings']['EMAIL_NOREPLY']        = 'no-reply@' . preg_replace('/^www\./', '', $http_host);
        $this->config['settings']['URL_RT']               = $http_scheme . '://' . $http_host . '/rt/';
        $this->config['settings']['URL_DEV_AUTH']         = str_replace($http_scheme . '://' . 'dev.', $http_scheme . '://' . 'rew:rew@dev.', $this->config['settings']['URL']);

        // Replace %host% and %root% For Web Addresses
        if (!empty($this->config['urls']) && is_array($this->config['urls'])) {
            foreach ($this->config['urls'] as $k => $v) {
                $this->config['urls'][$k] = str_replace(array(
                    '%domain%',
                    '%scheme%',
                    '%host%',
                    '%root%',
                ), array(
                    $http_domain,
                    $http_scheme,
                    $http_scheme . '://' . $http_host,
                    $http_scheme . '://' . ($this->httpHost->getDev() ? '' : 'www.') . $http_domain
                ), $v);
            }
        }

        // Replace %root% For Directory Paths
        if (!empty($this->config['dirs']) && is_array($this->config['dirs'])) {
            foreach ($this->config['dirs'] as $k => $v) {
                // If Uploads Just Do String Replace As Real Path Will Resolve Symlink.
                // Prevents Display Issues When Upload Features Are Used On A Development Site
                if ($k === 'UPLOADS') {
                    $this->config['dirs'][$k] = str_replace('%root%', $_SERVER['DOCUMENT_ROOT'], $v);
                } else {
                    $this->config['dirs'][$k] = realpath(str_replace('%root%', $_SERVER['DOCUMENT_ROOT'], $v)) . '/';
                }
            }
        }
    }

    /**
     * Get Instance of Singleton Object
     *
     * @return SettingsInterface
     * @deprecated Use dependency injection.
     */
    public static function getInstance()
    {
        return Container::getInstance()->get(SettingsInterface::class);
    }

    /**
     * Get Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Unset
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @return void
     * @deprecated See SettingsInterface::offsetUnset
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    /**
     * Unsets an option
     * @param string $name
     */
    public function offsetUnset($name)
    {
        $name = strtolower($name);
        unset($this->config[$name]);
    }

    /**
     * Isset
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @return bool
     * @deprecated See SettingsInterface::offsetExists
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * Checks if option is set
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        $name = strtolower($name);
        return isset($this->config[$name]);
    }

    /**
     * Setter
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @param mixed $value
     * @return SettingsInterface
     * @deprecated See SettingsInterface::offsetSet
     */
    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }

    /**
     * Sets an option
     * @param string $name
     * @param mixed $value
     * @return SettingsInterface
     */
    public function offsetSet($name, $value)
    {
        if (isset($name) && isset($value)) {
            $name = strtolower($name);
            $this->config[$name] = $value;
            // Set local
            if ($name == 'lang') {
                setlocale(LC_CTYPE, str_replace('-', '_', $value).'.utf8');
            }
        }
        return $this;
    }

    /**
     * Getter
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @return mixed
     * @deprecated See SettingsInterface::offsetGet
     */
    public function &__get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Gets an option
     * @param string $name
     * @return mixed
     */
    public function &offsetGet($name)
    {
        $name = strtolower($name);
        if (isset($name)) {
            // Check if we need to compile data for this key
            $methodName = 'loadOrBuild' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))) . 'Settings';
            if (!in_array($methodName, $this->_executedBuildMethods) && method_exists($this, $methodName)) {
                $this->_executedBuildMethods[] = $methodName;
                $this->$methodName();
            }

            if (array_key_exists($name, $this->config)) {
                return $this->config[$name];
            }
        }
        return null;
    }

    /**
     * Check if Visitor is from REW Office
     *
     *  __          __     _____  _   _ _____ _   _  _____
     *  \ \        / /\   |  __ \| \ | |_   _| \ | |/ ____|
     *   \ \  /\  / /  \  | |__) |  \| | | | |  \| | |  __
     *    \ \/  \/ / /\ \ |  _  /| . ` | | | | . ` | | |_ |
     *     \  /\  / ____ \| | \ \| |\  |_| |_| |\  | |__| |
     *      \/  \/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
     *
     * THIS WAY THERE BE DRAGONS!
     *
     * This function also controls passwordless access to the backend.
     * If you are making changes BE SUPER DUPER SURE that you aren't letting
     * untrusted people access to the backend. Use BrowserStack to test!
     *
     * @return boolean
     * @deprecated Use Environment::isREW
     */
    public static function isREW()
    {
        return Container::getInstance()->get(EnvironmentInterface::class)->isREW();
    }

    /**
     * Function to load the REW json config data
     *
     *  __          __     _____  _   _ _____ _   _  _____
     *  \ \        / /\   |  __ \| \ | |_   _| \ | |/ ____|
     *   \ \  /\  / /  \  | |__) |  \| | | | |  \| | |  __
     *    \ \/  \/ / /\ \ |  _  /| . ` | | | | . ` | | |_ |
     *     \  /\  / ____ \| | \ \| |\  |_| |_| |\  | |__| |
     *      \/  \/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
     *
     * DANGER WILL ROBINSON!
     *
     * This function also controls passwordless access to the backend.
     * If you are making changes BE SUPER DUPER SURE that you aren't letting
     * untrusted people access to the backend. Use BrowserStack to test!
     *
     * @return array
     * @deprecated Use Environment::getCoreConfig
     */
    public function getCoreConfig()
    {
        return Container::getInstance()->get(EnvironmentInterface::class)->loadMailCRMSettings()->getCoreConfig();
    }

    /**
     * Get DB Connection
     * @return DB
     */
    private function db()
    {
        if (!isset($this->_db)) {
            $this->_db = $this->_dbFactory->get();
        }
        return $this->_db;
    }

    /**
     * Get Setting from DB
     * <code>
     * <?php
     *
     * // Load setting from database
     * echo Settings::getInstance()->get('foo.bar'); // 'awesome'
     *
     * ?>
     * </code>
     * @param string $name
     * @param boolean $reload
     * @throws PDOException If database error occurs
     * @return string
     */
    public function get($name, $reload = false)
    {
        if (!$this instanceof self) {
            return call_user_func_array([self::getInstance(), __FUNCTION__], func_get_args());
        }

        try {
            if (isset($this->_settings[$name]) && !$reload) {
                return $this->_settings[$name];
            }
            $result = is_object($this->_db_stmt_select) ? $this->_db_stmt_select : $this->db()->prepare($this->_db_stmt_select);
            $result->execute(array('name' => $name));
            return $this->_settings[$name] = $result->fetchColumn();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Set Setting in DB
     * <code>
     * <?php
     *
     * // Save setting to database
     * Settings::getInstance()->set('foo.bar', 'awesome');
     *
     * ?>
     * </code>
     * @param string $name
     * @param string $value
     * @throws PDOException If database error occurrs
     * @return string
     */
    public function set($name, $value)
    {
        if (!$this instanceof self) {
            return call_user_func_array([self::getInstance(), __FUNCTION__], func_get_args());
        }

        try {
            $result = is_object($this->_db_stmt_insert) ? $this->_db_stmt_insert : $this->db()->prepare($this->_db_stmt_insert);
            $result->execute(array('name' => $name, 'value' => $value));
            return $this->_settings[$name] = is_null($value) ? null : (string) $value;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * This fetches feed titles from the DB if we don't have them already and adjusts the IDX_FEEDS array.
     * @param bool $forceReCache
     */
    public function loadOrBuildIdxFeedsSettings($forceReCache = false)
    {
        // This is only for multi-IDX
        if (empty($this->config['idx_feeds'])) {
            return;
        }

        // It would be ideal for the cache to be some infinite date in the future. However, because
        // a feed could be renamed on the dev and it shares the CMS db, it's not impossible for the
        // live file to become stale. Because of this, just cache for 12 hours.
        $cache = new Cache(array(
            'expires' => (60 * 60 * 12), // Expires in 12 hours
            'name' => 'json/settings.idx_feeds.json'
        ));

        // Use a hash to check if this needs to be re-built (i.e. a new feed was added).
        $hash = md5(serialize($this->config['idx_feeds']));

        if (!$forceReCache && $cache->checkCache()) {
            $feedJson = $cache->get();
            list ($feeds, $cachedHash) = json_decode($feedJson, true);
            if ($cachedHash == $hash) {
                $this->config['idx_feeds'] = $feeds;
                return;
            }
        }

        $criterias = $this->db()->fetchAll("SELECT `idx`,`criteria` FROM `rewidx_defaults` WHERE"
            . " `idx` IN (" . implode(", ", array_map(array($this->db(), 'quote'), array_keys($this->config['idx_feeds']))) . ")");
        foreach ($criterias as $c) {
            $idx = $c['idx'];
            $criteria = unserialize($c['criteria']);
            $feed_label = !empty($criteria['feed_label']) ? $criteria['feed_label'] : null;
            unset($criteria);
            if (!empty($feed_label)) {
                // Don't set if it's empty
                $this->config['idx_feeds'][$idx]['title'] = $feed_label;
            } else if (empty($this->config['idx_feeds'][$idx]['title'])) {
                // Fallback to ucwording the feed name if we must.
                $this->config['idx_feeds'][$idx]['title'] = ucwords($idx);
            }
        }

        $cache->save($cache->getName(), json_encode(array($this->config['idx_feeds'], $hash)));
    }

    /**
     * @inheritdoc
     */
    public function getVersion() {
        return $this->config['app_name'] . ' Version: ' . number_format($this->config['app_version'], 1) . (is_numeric($this->config['app_build']) ? '.' : '-') . $this->config['app_build'];
    }
}
