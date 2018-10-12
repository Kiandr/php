<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\User\SessionInterface;

/**
 * User_Session
 *
 */
final class User_Session extends User implements SessionInterface
{

    /**
     * Cookie Name
     * @var string
     */
    const COOKIE_NAME = 'rew-user';

    /**
     * Valid or Not
     * @var bool
     */
    private $is_valid = false;

    /**
     * User Row ID
     * @var int
     */
    private $user_id  = null;

    /**
     * User Row GUID
     * @var int
     */
    private $user_guid  = null;

    /**
     * Tracked User Information
     * @var array
     */
    private $info = array();

    /**
     * # of Viewed Listings
     * @var int
     */
    private $views;

    /**
     * Back URL
     * @var string
     */
    private $url_back = null;

    /**
     * Redirect URL
     * @var string
     */
    private $url_redirect = null;

    /**
     * Search URL
     * @var string
     */
    private $url_search = null;

    /**
     * RealtyTrac Back URL
     * @var string
     */
    private $rt_url_back = null;

    /**
     * Create User_Session, Load from Cookie if Set
     *
     * @return void
     */
    public function __construct()
    {

        // Set Search URL
        $this->url_search = Settings::getInstance()->SETTINGS['URL_IDX'];

        // Save User Session As Cookie (Remember Me)
        $this->setCookie(true);

        // Load User ID from Cookie
        if (!empty($_COOKIE[self::COOKIE_NAME])) {
            if ($this->authenticateUserToken($_COOKIE[self::COOKIE_NAME])) {
                Log::debug(__CLASS__, 'Loaded User ID from Cookie');
            }
        }
    }

    /**
     * Log Out User
     *
     * @return void
     */
    public function logOut()
    {

        // Track Logout Event
        if ($this->isValid()) {
            // Log Event: Lead Logged Out
            $event = new History_Event_Action_Logout(array(
                'ip' => $_SERVER['REMOTE_ADDR']
            ), array(
                new History_User_Lead($this->user_id())
            ));

            // Save to DB
            $event->save();
        }

        // Clear Info
        $this->info = array();

        // Reset User_Session
        $this->setValid(false);
        $this->setCookie(false);
        $this->setUsername(false);
        $this->setPassword(false);
        $this->setToken(false);

        // Remove from Session
        unset($_SESSION['user_session']);

        // Stop Tracking
        $secure = Http_Uri::getScheme() === 'https' ? true : false;
        setcookie(User_Visit::COOKIE_NAME, false, time() - 180 * 24 * 60 * 60, '/', Http_Host::getCookieDomain(), $secure, 1);
        if (session_id() != '') {
            session_destroy();
        }
    }

    /**
     * Set Username
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set Password
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set User Row ID
     *
     * @param int $user_id
     * @return void
     */
    public function setUserId($user_id)
    {

        // Set User Row ID
        $this->user_id = $user_id;
    }

    /**
     * Get User Row ID
     *
     * @return null|int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set User Row GUID and then save it for session use
     *
     * @param binary $user_guid
     * @return void
     */
    public function setUserGuid($user_guid)
    {

        // Set User Row GUID
        $this->user_guid = $user_guid;

        $this->_buildAuthToken($user_guid);
    }

    /**
     * Get User Row GUID
     *
     * @return null|int
     */
    public function getUserGuid()
    {
        return $this->user_guid;
    }

    /**
     * Set the cookie for the authentication token
     *
     * @param binary $user_guid
     * @global array $_COOKIE
     */
    public function setUserToken($user_guid)
    {

        // Save User ID to Cookie
        $this->setCookie(true);
        $this->_buildAuthToken($user_guid);
    }

    /**
     * Set If User Is Valid
     *
     * @param bool $is_valid
     * @return void
     */
    public function setValid($is_valid)
    {
        $this->is_valid = $is_valid;
    }

    /**
     * Check If User is Valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->is_valid;
    }

    /**
     * Get User Row ID
     *
     * @return null|int
     * @deprecated Use User_Session::getUserGuid instead
     */
    public function user_id()
    {
        return $this->user_id;
    }

    /**
     * Get User Row GUID
     *
     * @return null|int
     * @deprecated Use User_Session::getUserId() instead
     */
    public function user_guid()
    {
        return $this->getUserGuid();
    }

    /**
     * Get Username
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function password()
    {
        return $this->password;
    }

    /**
     * Set Last Search URL
     *
     * @param string $url_search
     * @return void
     */
    public function setSearchUrl($url_search)
    {
        $this->url_search = $url_search;
    }

    /**
     * Get Last Search URL
     *
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->url_search;
    }

    /**
     * Get Last Search URL
     *
     * @return string
     * @deprecated Use User_Session::getSearchUrl() instead
     */
    public function url_search()
    {
        return $this->getSearchUrl();
    }

    /**
     * Set Redirect URL
     *
     * @param string $url_redirect
     * @return void
     */
    public function setRedirectUrl($url_redirect)
    {
        $this->url_redirect = $url_redirect;
    }

    /**
     * Get Redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->url_redirect;
    }

    /**
     * Get Redirect URL
     *
     * @return string
     * @deprecated Use User_Session::getRedirectUrl() instead
     */
    public function url_redirect()
    {
        return $this->getRedirectUrl();
    }

    /**
     * Set Back URL
     *
     * @param string $url_back
     * @return void
     */
    public function setBackUrl($url_back)
    {
        $this->url_back = $url_back;
    }

    /**
     * Get Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->url_back;
    }

    /**
     * Get Back URL
     *
     * @return string
     * @deprecated Use User_Session::getBackUrl() instead
     */
    public function url_back()
    {
        return $this->getBackUrl();
    }

    /**
     * Set RealtyTrac Back URL
     *
     * @param string $rt_url_back
     * @return void
     */
    public function setRTBackUrl($rt_url_back)
    {
        $this->rt_url_back = $rt_url_back;
    }

    /**
     * Get RealtyTrac Back URL
     *
     * @return string
     */
    public function getRTBackUrl()
    {
        return $this->rt_url_back;
    }

    /**
     * Get RealtyTrac Back URL
     *
     * @return string
     * @deprecated Use User_Session::getRTBackUrl() instead
     */
    public function rt_url_back()
    {
        return $this->getRTBackUrl();
    }

    /**
     * Get User Information by Field Name
     *
     * @param string $name
     * @return mixed
     */
    public function info($name)
    {
        if (isset($this->info[$name])) {
            return $this->info[$name];
        } else {
            return null;
        }
    }

    /**
     * Save User Information
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function saveInfo($name, $value)
    {
        $this->info[$name] = $value;
    }

    /**
     * Return # of Viewed Listings
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Increment # of Viewed Listings
     *
     * @return void
     */
    public function incrementViews()
    {
        $this->views++;
    }

    /**
     * Get HTML Formatted User information
     *
     * @return string
     * @uses Backend_Lead::$phone_status
     */
    public function formatUserInfo()
    {

        // Phone Statuses (Convert Int to String)
        $phone_status = Backend_Lead::$phone_status;

        // Process Information, Format Accordingly, Build HTML
        $info = $this->info;
        foreach ($info as $column => $data) {
            $title = $column;
            $value = false;
            $html = false;
            if (empty($data)) {
                continue;
            }
            switch ($column) {
                // Assigned Agent
                /*case 'agent' :
            		$title = 'Assigned Agent';
            		if ($info['agent'] == 1 && in_array($info['status'], array('pending', 'rejected'))) break;
            		$value = $info['agent'] . ' (' . ucwords($info['status']) . ')' . ($info['auto_rotate'] == 'true' && $info['status'] == 'pending' ? ' (Auto-Rotate)' : '');
            		break;*/
                // Lead Name
                case 'first_name':
                    $title = 'Name';
                    $html = true;
                    if (!empty($info['id'])) {
                        $value = '<a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $info['id'] . '">' . $info['first_name'] . ' ' . $info['last_name'] . '</a>';
                    } else {
                        $value = $info['first_name'] . ' ' . $info['last_name'];
                    }
                    break;
                case 'last_name':
                    break;
                // Email Address
                case 'email':
                    // Email Flags
                    $flags = array();
                    if ($info['fbl'] == 'true') {
                        $flags[] = 'Reported';
                    }
                    if ($info['bounced'] == 'true') {
                        $flags[] = 'Bounced';
                    }
                    if ($info['opt_marketing'] == 'out') {
                        $flags[] = 'Opt-Out';
                    }
                    if ($info['verified'] == 'yes') {
                        $flags[] = 'Verified';
                    }
                    if (!Validate::verifyWhitelisted($info['email']) && (Validate::verifyRequired($info['email']) || !empty(Settings::getInstance()->SETTINGS['registration_verify'])) && $info['verified'] != 'yes') {
                        $flags[] = 'Unverified';
                    }
                    // Email Link
                    $value = '<a href="mailto:' . $data . '">' . $data . '</a>' . (!empty($flags) ? ' (' . implode(', ', $flags) . ')' : '');
                    $html = true;
                    break;
                // Alt. Email
                case 'email_alt':
                    $html = true;
                    $value = '<a href="mailto:' . $data . '">' . $data . '</a>';
                    break;
                // Primary Phone
                case 'phone':
                    $status = $phone_status[$info['phone_home_status']];
                    $value = $info['phone'] . (!empty($status) ? ' (' . $status  . ')' : '');
                    break;
                // Secondary Phone
                case 'phone_cell':
                    $status = $phone_status[$info['phone_cell_status']];
                    $value = $info['phone_cell'] . (!empty($status) ? ' (' . $status  . ')' : '');
                    break;
                // Work Phone
                case 'phone_work':
                    $status = $phone_status[$info['phone_work_status']];
                    $value = $info['phone_work'] . (!empty($status) ? ' (' . $status  . ')' : '');
                    break;
                // Lead Details
                case 'referer':
                case 'heat':
                case 'phone_fax':
                case 'comments':
                case 'address1':
                case 'address2':
                case 'address3':
                case 'city':
                case 'country':
                    $value = $data;
                    break;
                case 'state':
                case 'zip':
                    $title = Locale::spell($column);
                    $value = $data;
                    break;
                // Social Networks
                case 'network_facebook':
                case 'network_google':
                case 'network_microsoft':
                    $data = json_decode($data, true);
                    if (!empty($data) && is_array($data)) {
                        $html = true;
                        $title = ucwords(str_replace('network_', '', $column));
                        $value = !empty($data['link']) ? '<a href="' . $data['link'] . '">' . $data['link'] . '</a>' : 'Connected';
                    }
                    break;
                // Tracking Details
                case 'last_form':
                case 'last_call':
                case 'last_email':
                    $data = json_decode($data, true);
                    $value = $data['type'] . ' on ' . date('F jS, Y \a\t g:ia', $data['timestamp']) . '.';
                    break;
                // Tracking Numbers
                case 'num_calls':
                case 'num_emails':
                case 'num_visits':
                case 'num_pages':
                case 'num_forms':
                case 'num_favorites':
                case 'num_listings':
                case 'num_searches':
                case 'num_saved':
                    $title = 'Number Of ' . Locale::spell(str_replace('num_', '', $column));
                    $value = number_format($data);
                    break;
                // Search Preferences
                case 'search_type':
                case 'search_city':
                case 'search_subdivision':
                    $value = $data;
                    break;
                // Price Range
                case 'search_minimum_price':
                case 'search_maximum_price':
                    $value = number_format($data);
                    break;
                // Agent Remarks
                case 'remarks':
                    $title = 'Agent Remarks';
                    $value = $data;
                    break;
                // Quick Notes
                case 'notes':
                    $title = 'Quick Notes';
                    $value = $data;
                    break;
                // Lead Status
                case 'status':
                    $value = ucwords($data) . ($data == 'rejected' && !empty($info['rejectwhy']) ? ': ' . $info['rejectwhy'] : '');
                    break;
                // Lead Forms
                case 'forms':
                    $value = str_replace(',', ', ', $data);
                    break;
                // Join Date
                case 'timestamp':
                    $title = 'Join Date';
                    $value = date('F jS, Y \a\t g:ia', strtotime($data));
                    break;
                // Last Active
                case 'timestamp_active':
                    $title = 'Last Active';
                    $value = date('F jS, Y \a\t g:ia', strtotime($data));
                    break;
                // Opt-In: Campaigns
                case 'opt_marketing':
                    $title = 'Subscribed to Campaigns';
                    $value = $data == 'in' ? 'Yes' : 'No';
                    break;
                // Opt-In: Searches
                case 'opt_searches':
                    $title = 'Subscribed to Searches';
                    $value = $data == 'in' ? 'Yes' : 'No';
                    break;
                // Preferred Contact Method
                case 'contact_method':
                    $title = 'Preferred Contact Method';
                    $value = ucwords($data);
                    break;
                // View Listings
                case 'viewed':
                    $title = 'Viewed Listings';
                    if (is_array($data)) {
                        $value = implode(', ', array_map(function ($mls_number) {
                            // @TODO: Link to Details
                            return $mls_number;
                        }, $data));
                    }
                    break;
            }
            // Generate HTML
            if (!empty($value)) {
                $title = ucwords(str_replace('_', ' ', $title));
                $output .= '<strong>' . $title . ':</strong> ' . (!empty($html) ? $value : htmlspecialchars($value)) . '<br>' . PHP_EOL;
            }
        }
        // Return HTML
        return $output;
    }

    /**
     * Get User Row
     *
     * @return array
     * @global Database $db_users
     */
    public function getRow()
    {
        $query = DB::get()->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1;");
        $query->execute(array('id' => $this->user_id()));
        return $query->fetch();
    }

    /**
     * Get list of user's saved favorites
     * @param IDXInterface $idx
     * @return array
     */
    public function getSavedListings(IDXInterface $idx)
    {
        $user_id = $this->user_id();
        if (empty($user_id)) {
            return array();
        }
        $query = DB::get()->prepare("SELECT `mls_number`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings` WHERE `idx` = :idx AND `user_id` = :user_id AND `agent_id` IS NULL AND `associate` IS NULL;");
        $query->execute(array('user_id' => $user_id, 'idx' => $idx->getLink()));
        $saved = $query->fetchAll(PDO::FETCH_COLUMN);
        return array_combine($saved, $saved);
    }

    /**
     * Get list of user's dismissed listings
     * @param IDXInterface $idx
     * @return array
     */
    public function getDismissedListings(IDXInterface $idx)
    {
        $user_id = $this->user_id();
        if (empty($user_id)) {
            return array();
        }
        $query = DB::get()->prepare("SELECT `mls_number`, UNIX_TIMESTAMP(`timestamp`) AS `timestamp` FROM `users_listings_dismissed` WHERE `idx` = :idx AND `user_id` = :user_id;");
        $query->execute(array('user_id' => $user_id, 'idx' => $idx->getLink()));
        $dismissed = $query->fetchAll(PDO::FETCH_COLUMN);
        return array_combine($dismissed, $dismissed);
    }

    /**
     * Set basic user information (excluding password)
     *
     * @param array $user
     */
    private function setUserInfo(array $user)
    {
        $this->setUserGuid($user['guid']);
        $this->setUserId($user['id']);
        $this->setUsername($user['email']);
        foreach ($user as $key => $val) {
            $this->info[$key] = $val;
        }
    }

    /**
     * Validate User Session
     *
     * @return bool
     */
    public function validate()
    {

        if (!$this->isValid()) {
            if (is_null($this->user_id())) {
                return false;
            } else {
                $login_check = $this->getRow();
                if (!empty($login_check)) {
                    $this->setUserInfo($login_check);
                    $this->setValid(true);
                    return true;
                } else {
                    $this->setUserId(null);
                    $this->setValid(false);
                    return false;
                }
            }
        } else {
            $login_check = $this->getRow();
            if (!empty($login_check)) {
                $this->setUserInfo($login_check);
                return true;
            } else {
                // User session is valid but they are no longer in the DB!
                $this->setUserId(null);
                $this->setValid(false);
                return false;
            }
        }
    }

    /**
     * Is email used on an existing user
     * @param $username email
     * @return bool
     */
    public function exists($username)
    {
        $db = DB::get();
        $query = $db->prepare("SELECT `guid`, `id`, `password` FROM `users` WHERE `email` = :username LIMIT 1;");
        $query->execute(array(':username' => $username));
        $results = $query->fetch();
        return !empty($results);
    }

    /**
     * Get Current User Session
     *
     * @return User_Session
     */
    public static function get()
    {
        if ( (php_sapi_name() !== 'cli') && (session_status() == PHP_SESSION_NONE) ) @session_start();
        $user = isset($_SESSION['user_session']) ? $_SESSION['user_session'] : false;

        // New User Session
        if (!($user instanceof User_Session)) {
            $user = new self ();
        }

        // Validate User Session
        if (!$user->isValid()) {
            $user->validate();
        }

        // Save to Session & Return User Session
        return $_SESSION['user_session'] = $user;
    }

    /**
     * Authenticate the user token and if successful
     *
     * @param binary $token
     * @return boolean
     */
    private function authenticateUserToken($token)
    {

        // Get the User ID from the guid
        $query = DB::get()->prepare("SELECT `id` FROM `users` WHERE `guid` = :guid LIMIT 1;");
        $query->execute(array('guid' => $token));
        if ($auth_user = $query->fetchColumn()) {
            // Set the user ID
            $this->setUserId($auth_user);

            // Authentication was successful
            return true;
        }

        // Authentication Failed
        return false;
    }

    /**
     * Authenticate User in Database
     * @param string $username
     * @param string $password
     * @param DBInterface $db
     * @throws PDOException
     */
    public function authenticate($username, $password = null, DBInterface $db = null)
    {
        // DB Connection
        $db = !is_null($db) ? $db : DB::get();

        // Get hash to compare against
        $query = $db->prepare("SELECT `guid`, `id`, `password` FROM `users` WHERE `email` = :username LIMIT 1;");
        $query->execute(array(':username' => $username));
        $results = $query->fetch();

        //User does not exist
        if (!$results) {
            $this->incrementLoginAttempts();
            return false;
        }

        //Set Username
        $this->setUsername($username);

        if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) {
            //Incorrect Passwords (with REW exception)
            if (!(Settings::isREW() && empty($password)) && (empty($password) || !password_verify($password, $results['password']))) {
                $this->incrementLoginAttempts();
                return false;
            }

            //Set Password
            $this->setPassword($results['password']);
        }

        $this->setUserId($results['id']);

        //Build/Set Authentication Token
        $this->setUserGuid($results['guid']);

        return true;
    }

    /**
     * Get Cookie Name
     * @return string
     */
    public static function getCookieName()
    {
        return self::COOKIE_NAME;
    }

    /**
     * Update an existing authentication with changed credentials
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function update($username, $password = null)
    {

        //Set Username/Password
        $this->setUsername($username);
        if (!empty($password)) {
            $this->setPassword($password);
        }

        //Build/Set Authentication Token
        $this->_buildAuthToken();
    }

    /**
     * @return string|NULL
     */
    public function getPhotoUrl()
    {
        // Use image from connected social media profile
        $networks = ['facebook', 'google', 'microsoft', 'linkedin', 'twitter', 'yahoo'];
        foreach ($networks as $network) {
            $index = sprintf('network_%s', $network);
            if (isset($this->info[$index])) {
                $json = $this->info[$index];
                if (!empty($json)) {
                    $profile = json_decode($json, true);
                    if (!empty($profile['image'])) {
                        return $profile['image'];
                    }
                }
            }
        }
        // Fallback to gravatar photo URL
        return $this->getGravatarPhotoUrl();
    }

    /**
     * @param int $size
     * @param string $rating
     * @param string $default
     * @return string
     */
    protected function getGravatarPhotoUrl($size = 80, $rating = 'pg', $default = 'mm')
    {
        $gravatarUrl = '//www.gravatar.com/avatar/%s?s=%s&r=%s&d=%s';
        $hash = md5(strtolower(trim($this->info['email'])));
        return sprintf($gravatarUrl, $hash, $size, $rating, $default);
    }
}
