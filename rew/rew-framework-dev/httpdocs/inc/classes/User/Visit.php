<?php

/**
 * User Tracking Class
 */
class User_Visit
{

    /**
     * Cookie Name
     * @var string
     */
    const COOKIE_NAME = 'rew-visit';

    /**
     * Current Page
     * @var string
     */
    private $page;

    /**
     * Visit Referer
     * @var string
     */
    private $referer;

    /**
     * Visit Keywords
     * @var string
     */
    private $keywords;

    /**
     * Visit ID
     * @var int
     */
    private $id;

    /**
     * Database Connection
     * @var DB
     */
    private $db;

    /**
     * User ID
     * @var int
     */
    private $user_id;

    /**
     * IP Address
     * @var string
     */
    private $ip;

    /**
     * User Agent
     * @var string
     */
    private $ua;

    /**
     * Track Visit
     * @param DB $db
     * @return void
     */
    public function __construct(DB $db)
    {

        // Set Session Database
        $this->setDB($db);

        // Referer Data
        $source = self::parseReferer($_SERVER['HTTP_REFERER']);
        if (!empty($source)) {
            $this->referer = $source['source'];
            $this->keywords = $source['keywords'];
        } else {
            // Parse Referer URL
            $url = parse_url($_SERVER['HTTP_REFERER']);

            // Check Empty Host
            if (!empty($url['host'])) {
                // Trim www.
                $url['host'] = str_replace('www.', '', $url['host']);

                // Check Hostname, Skip if from Same Domain
                if (stristr($url['host'], $_SERVER['HTTP_HOST']) !== false) {
                    $url['host'] = '';
                }

                // Format Known Hosts
                $check = array('search.yahoo.com', 'mail.yahoo.com', 'mail.yahoo.net', 'mail.live.com', 'mail.comcast.net', 'webmail.aol.com');
                foreach ($check as $host) {
                    if (stristr($url['host'], $host) !== false) {
                        $url['host'] = $host;
                    }
                }

                // Save Original Referer
                if (!empty($url['host'])) {
                    $this->referer = $url['host'];
                }
            }
        }

        // PPC tracking override due to SSL
        if (isset($_GET['gclid'])) {
            $this->referer = 'Google PPC';
            if (isset($_GET['_ppc'])) {
                $this->keywords = $_GET['_ppc'];
            }
        }

        // User Agent
        $this->ua = $_SERVER['HTTP_USER_AGENT'];

        // IP Address
        $this->ip = $_SERVER['REMOTE_ADDR'];

        try {
            // Prepare INSERT Query
            $insert = $this->db->prepare("INSERT INTO `users_sessions` SET "
                . "`referer`    = :referer, "
                . "`keywords`   = :keywords, "
                . "`ua`         = :ua, "
                . "`ip`         = INET_ATON(:ip), "
                . "`timestamp`	= NOW()"
            . ";");

            // Save Session Data
            $insert->execute(array(
                'referer'   => $this->referer,
                'keywords'  => $this->keywords,
                'ua'        => $this->ua,
                'ip'        => $this->ip
            ));

            // Set Session ID
            $this->id = $this->db->lastInsertId();

        // Error occurred
        } catch (PDOException $e) {
            //echo $e->getMessage();
            Log::error($e);
        }
    }

    /**
     * Set the db connection (PDO)
     * @param DB $db
     * @return void
     */
    public function setDB(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Set User ID
     * @param int $username
     * @return void
     */
    public function setUserID($user_id)
    {
        if (!empty($user_id)) {
            if (empty($this->user_id)) {
                try {
                    // Find user in database
                    $check_id = $this->db->prepare("SELECT `id`, `guid` FROM users WHERE `id` = :user_id LIMIT 1;");
                    $check_id->execute(array('user_id' => $user_id));
                    $check_id = $check_id->fetch();
                    if (!empty($check_id)) {
                        $this->user_id = $user_id;

                        // Associate this session with this user
                        $update = $this->db->prepare("UPDATE `users_sessions` SET `user_id` = :user_id WHERE `id` = :session_id LIMIT 1;");
                        $update->execute(array('user_id' => $this->user_id, 'session_id' => $this->id));

                        // Save referring domain
                        if (!empty($this->referer)) {
                            $update = $this->db->prepare("UPDATE `users` SET `referer` = :referer WHERE `referer` = '' AND `id` = :user_id LIMIT 1;");
                            $update->execute(array('referer' => $this->referer, 'user_id' => $this->user_id));

                            // Save referring keywords
                            if (!empty($this->keywords)) {
                                $update = $this->db->prepare("UPDATE `users` SET `keywords` = :keywords WHERE `keywords` = '' AND `id` = :user_id LIMIT 1;");
                                $update->execute(array('keywords' => $this->keywords, 'user_id' => $this->user_id));
                            }
                        }

                        // Increment # of user visits
                        $update = $this->db->prepare("UPDATE `users` SET `num_visits` = `num_visits` + 1 WHERE `id` = :user_id LIMIT 1;");
                        $update->execute(array('user_id' => $this->user_id));

                        // Set cookie to keep session active
                        $secure = Http_Uri::getScheme() === 'https' ? true : false;
                        setcookie(self::COOKIE_NAME, $check_id['guid'], time() + 180 * 24 * 60 * 60, '/', Http_Host::getCookieDomain(), $secure, 1);

                        // Current visit count
                        $visits_count = $this->db->prepare("SELECT `num_visits` FROM `users` WHERE `id` = :user_id LIMIT 1;");
                        $visits_count->execute(array('user_id' => $user_id));
                        $num_visits = $visits_count->fetchColumn();

                        // Run hook
                        Hooks::hook(Hooks::HOOK_LEAD_VISIT)->run($this->user_id, $num_visits, $this->referer, $this->keywords);
                    } else {
                        // User not found
                        $this->user_id = false;
                        $secure = Http_Uri::getScheme() === 'https' ? true : false;
                        setcookie(self::COOKIE_NAME, false, time() - 180 * 24 * 60 * 60, '/', Http_Host::getCookieDomain(), $secure, 1);
                    }

                // Error occurred
                } catch (PDOException $e) {
                    //echo $e->getMessage();
                    Log::error($e);
                }
            }
        } else {
            $this->user_id = false;
        }
    }

    /**
     * Authenticate the UserTrack token with the user
     *
     * @param binary $token
     * @return boolean
     */
    public function authenticateUserTrack($token)
    {

        $check_id = $this->db->prepare("SELECT `id` FROM users WHERE `guid` = :user_guid LIMIT 1;");
        $check_id->execute(array('user_guid' => $token));
        if ($auth_user = $check_id->fetchColumn()) {
            $this->setUserID($auth_user);
            return true;
        }

        return false;
    }

    /**
     * Get User ID
     * @return int
     */
    public function getUserID()
    {
        return $this->user_id;
    }

    /**
     * Record the page they have requested in the DB
     * @return void
     */
    public function recordPage()
    {
        try {
            // Get the ID of the Page
            $this->page = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];
            $page_id = $this->db->prepare("SELECT `id` FROM `users_pages` WHERE `hash` = UNHEX(MD5(:page)) LIMIT 1;");
            $page_id->execute(array('page' => $this->page));
            $page_id = $page_id->fetchColumn();

            // Unknown page, track it
            if (empty($page_id)) {
                $insert = $this->db->prepare("INSERT INTO `users_pages` SET `url` = :page, `hash` = UNHEX(MD5(:page));");
                $insert->execute(array('page' => $this->page));
                $page_id = $this->db->lastInsertId();
            }

            // Get the ID of the referer
            $referer = $_SERVER['HTTP_REFERER'];
            if (!empty($referer)) {
                $referer_id = $this->db->prepare("SELECT `id` FROM `users_pages` WHERE `hash` = UNHEX(MD5(:referer)) LIMIT 1;");
                $referer_id->execute(array('referer' => $referer));
                $referer_id = $referer_id->fetchColumn();

                // Unknown referer, track it
                if (empty($referer_id)) {
                    $insert = $this->db->prepare("INSERT INTO `users_pages` SET `url` = :referer, `hash` = UNHEX(MD5(:referer));");
                    $insert->execute(array('referer' => $referer));
                    $referer_id = $this->db->lastInsertId();
                }
            }

            // Record this page view
            $insert = $this->db->prepare("INSERT INTO `users_pageviews` SET `session_id` = :session_id, `page_id` = :page_id, `referer_id` = :referer_id, `timestamp` = NOW();");
            $insert->execute(array('session_id' => $this->id, 'page_id' => $page_id, 'referer_id' => $referer_id));

            // Increment `num_pages` and Update `timestamp_active`
            if (!empty($this->user_id)) {
                $update = $this->db->prepare("UPDATE `users` SET `num_pages` = `num_pages` + 1, `timestamp_active` = NOW() WHERE `id` = :user_id LIMIT 1;");
                $update->execute(array('user_id' => $this->user_id));
            }

        // Error occurred
        } catch (PDOException $e) {
            //echo $e->getMessage();
            Log::error($e);
        }
    }

    /**
     * Parse URL to Return Search Engine Details (Source, Keywords, PPC)
     * @param string $url
     * @return array[source,keyword]|null
     */
    public static function parseReferer($url)
    {
        // Search Engines
        $engines = array(
            'Google PPC' => array(
                'host_pattern_preg' => '!(googleadservices|google)(\\.[a-z]+)+$!',
                'path_pattern' => '!^/aclk!',
                'query_variable' => 'q'
            ),
            'Google Images' => array(
                'host_pattern_preg' => '!google(\\.[a-z]+)+$!',
                'path_pattern' => '!^/imgres!',
                'query_variable' => 'q'
            ),
            'Google' => array(
                'host_pattern_preg' => '!google(\\.[a-z]+)+$!',
                'path_pattern' => '!^/!',
                'query_variable' => 'q'
            ),
            'Yahoo!' => array(
                'host_pattern_preg' => '!([a-z]+\\.)*search.yahoo.[a-z]+!',
                'path_pattern' => '!^/(search|tablet)!',
                'query_variable' => 'p'
            ),
            'Live search' => array(
                'host_pattern_preg' => '!search\\.(live|msn)\\.[a-z]+!',
                'path_pattern' => '!^/results.aspx!',
                'query_variable' => 'q'
            ),
            'Bing' => array(
                'host_pattern_preg' => '!bing\\.[a-z]+!',
                'path_pattern' => '!^/search!',
                'query_variable' => array('q', 'MT')
            ),
            'MyWebSearch' => array(
                'host_pattern_preg' => '!mywebsearch(\\.[a-z]+)+$!',
                'path_pattern' => '!^/!',
                'query_variable' => 'searchfor'
            ),
            'Ask.com' => array(
                'host_pattern_preg' => '!ask(\\.[a-z]+)+$!',
                'path_pattern' => '!^/!',
                'query_variable' => 'q'
            ),
            'AOL' => array(
                'host_pattern_preg' => '!search.aol(\\.[a-z]+)+$!',
                'path_pattern' => '!^/!',
                'query_variable' => array('q', 'query')
            ),
            'Facebook' => array(
                'host_pattern_preg' => '!facebook.com$!',
                'path_pattern' => '!^/l.php!',
                'query_variable' => 'q'
            )
        );
        // Parse URL
        $url_data = parse_url($url);
        // Detect Google PPC
        if (preg_match('!googleadservices!', $url_data['host'])) {
            return array('source' => 'Google PPC');
        }
        // Google PPC Auto-Tagged
        if (preg_match('!' . Http_Host::getDomain() . '!', $url_data['host'])) {
            parse_str($url_data['query'], $query_data);
            if (!empty($query_data['gclid'])) {
                return array('source' => 'Google PPC');
            }
        }
        // Detect Search Engine
        foreach ($engines as $engine_name => $engine) {
            if (preg_match($engine['host_pattern_preg'], $url_data['host']) && preg_match($engine['path_pattern'], $url_data['path'])) {
                parse_str($url_data['query'], $query_data);
                // Find Keywords
                $variables = is_array($engine['query_variable']) ? $engine['query_variable'] : array($engine['query_variable']);
                foreach ($variables as $variable) {
                    $keywords = $query_data[$variable];
                    if (!empty($keywords)) {
                        return array('source' => $engine_name, 'keywords' => $keywords);
                    }
                }
            }
        }
        // Unknown
        return null;
    }

    /**
     * "You cannot serialize or unserialize PDO instances"
     * @return array
     */
    public function __sleep()
    {
        $props = get_object_vars($this);
        unset($props['db']);
        return array_keys($props);
    }
}
