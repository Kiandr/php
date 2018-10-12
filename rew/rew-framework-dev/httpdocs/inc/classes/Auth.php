<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\AuthInterface;

/**
 * Auth
 *
 */
final class Auth extends User implements AuthInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * Auth Table
     * @var string
     */
    public static $table = 'auth';

    /**
     * Is Validated
     * @var bool
     */
    private $valid = false;

    /**
     * Auth ID
     * @var int
     */
    private $id;

    /**
     * Auth Info (Loaded from Database)
     * @var array
     */
    private $info = array();

    /**
     * Auth Data (Loaded from Session)
     * @var array
     */
    private $data = array();

    /**
     * Auth Record
     * @var array
     */
    private $auth = array();

    /**
     * @var \REW\Core\Interfaces\History\UserInterface
     */
    private $historyUser;

    /**
     * Authenticated
     * @var bool
     */
    private static $validated;

    /**
     * Encryption Secret
     * @deprecated
     * @var string
     */
    const AUTH_SALT = 'Xemm4H2E1qmZaaorRK76';

    /**
     * Encryption cost
     * @deprecated
     * @var string
     */
    const AUTH_BCRYPT_COST = 10;

    /**
     * Failed Attempt Limit
     * @deprecated
     * @var string
     */
    const AUTH_RATE_LIMIT = 1000;

    /**
     * Failed Attempts timeframe
     * @deprecated
     * @var string
     */
    const AUTH_RATE_LIMIT_LENGTH = 1;

    /**
     * Too-Many_attempts ban length
     * @deprecated
     * @var string
     */
    const AUTH_BAN_LENGTH = 5;

    /**
     * Auth Type
     * @var int
     */
    private $type = self::TYPE_AGENT;

    /******************* Constructor *******************/

    /**
     * Setup Auth
     * @param int $type
     */
    public function __construct($type = self::TYPE_AGENT)
    {
        $this->setType($type);

        // Get Token from Cookie
        $this->_loadTokenFromCookie();
    }

    /******************* Setters *******************/

    /**
     * Set Auth Type
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * Set Valid
     * @param bool $valid
     * @return void
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    /******************* Getters *******************/

    /**
     * Get Auth Type
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get Account Table
     * @return string
     */
    public function getTable()
    {

        // Auth Table
        $tables = array(
            static::TYPE_AGENT        => 'agents',
            static::TYPE_ASSOCIATE    => 'associates',
            static::TYPE_LENDER       => 'lenders'
        );

        // Require Auth Type
        return $tables[$this->type];
    }

    /**
     * Get Auth' Name
     * @return string|NULL
     */
    public function getName()
    {
        return $this->info('first_name') . ' ' . $this->info('last_name');
    }

    /**
     * Get URL to Edit Preferences
     * @return string|NULL
     */
    public function getEditURL()
    {
        $id = $this->info('id');
        if (!empty($id)) {
            $type = $this->getType();
            if ($type === static::TYPE_AGENT) {
                return Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/edit/?id=' . $id;
            } elseif ($type === static::TYPE_ASSOCIATE) {
                return Settings::getInstance()->URLS['URL_BACKEND'] . 'associates/associate/edit/?id=' . $id;
            } elseif ($type === static::TYPE_LENDER) {
                return Settings::getInstance()->URLS['URL_BACKEND'] . 'lenders/lender/edit/?id=' . $id;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        // We cannot serialize historyUser because it utilizes PDO.
        return array_diff(array_keys(get_object_vars($this)), ['historyUser']);
    }

    /**
     * Get History User
     * @return History_User
     */
    public function getHistoryUser()
    {
        if ($this->historyUser === null) {
            // Is Lender
            if ($this->isLender()) {
                $this->historyUser = new History_User_Lender($this->info('id'));
                // Is Agent
            } elseif ($this->isAgent()) {
                $this->historyUser = new History_User_Agent($this->info('id'));
                // Is Associate
            } elseif ($this->isAssociate()) {
                $this->historyUser = new History_User_Associate($this->info('id'));
            }
        } else {
            $this->historyUser->setUser($this->info('id'));
        }

        return $this->historyUser;
    }

    /**
     * Get / Set Data (Loaded from Session)
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
    public function data($name, $value = null)
    {
        // Get Data
        if (is_string($name) && is_null($value)) {
            return $this->data[$name];
        }
        // Set Data
        if (is_string($name) && !is_null($value)) {
            $this->data[$name] = $value;
        }
    }

    /**
     * Get / Set Info (Loaded from Database)
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
    public function info($name, $value = null)
    {
        // Get Info
        if (is_string($name) && is_null($value)) {
            // Array access
            if (strpos($name, '.') !== false) {
                return $this->_resolveDotNotation($this->info, $name);
            } else {
                return $this->info[$name];
            }
        }
        // Set Info
        if (is_string($name) && !is_null($value)) {
            $this->info[$name] = $value;
        }
    }

    /**
     * Get Info
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Get Valid
     *
     * @return bool $this->valid
     */
    public function isValid()
    {
        return $this->valid;
    }

    /******************* Permissions *******************/

    /**
     * Check If Lender
     * @return bool
     */
    public function isLender()
    {
        return ($this->getType() === static::TYPE_LENDER);
    }

    /**
     * Check If Agent
     * @return bool
     */
    public function isAgent()
    {
        return ($this->getType() === static::TYPE_AGENT);
    }

    /**
     * Check If Associate
     * @return bool
     */
    public function isAssociate()
    {
        return ($this->getType() === static::TYPE_ASSOCIATE);
    }

    /**
     * Is Super Admin
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return ($this->getType() === static::TYPE_AGENT && $this->info('id') === '1');
    }

    /**
     * Is Admin (Has Admin Permissions)
     *
     * @return bool
     */
    public function isAdmin()
    {
        return ($this->getType() === static::TYPE_AGENT && $this->info('permissions_admin') > 0);
    }

    /**
     * Check Permissions
     * @param int $permission
     * @return bool True, If Authorized
     */
    public function hasPermission($permission)
    {
        return ($this->getType() === static::TYPE_AGENT && $this->info('permissions_user') & $permission) ? true : false;
    }

    /**
     * Check Admin Permissions
     * @param int $permission
     * @return bool True, If Authorized
     */
    public function adminPermission($permission)
    {
        return ($this->getType() === static::TYPE_AGENT && $this->info('permissions_admin') & $permission) ? true : false;
    }

    /************* Notices *************/

    /**
     * Save Notices to $_SESSION
     * @param array $success
     * @param array $errors
     * @param array $warnings
     * @return void
     * @deprecated 4.8
     * @deprecated Replaced by REW\Backend\NoticesCollectionInterface::add
     */
    public function setNotices(&$success = array(), &$errors = array(), &$warnings = array())
    {
        $notices = array();
        // Success
        if (!empty($success)) {
            $notices['success']     = $success;
        }
        // Errors
        if (!empty($errors)) {
            $notices['error']      = $errors;
        }
        // Warnings
        if (!empty($warnings)) {
            $notices['warning']    = $warnings;
        }
        // Save to $_SESSION
        $_SESSION['notices'] = serialize($notices);
    }

    /************* Validation *************/

    /**
     * Validate Username (Must be Unique)
     * @param string $username
     * @param int|null $auth
     * @param array $errors
     * @return boolean
     */
    public function validateUsername($username, $auth, &$errors = array())
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(AuthInterface::class, __FUNCTION__, [$username, $auth, &$errors]);
        }

        // DB Connection
        $db = DB::get();

        // Require Valid Username
        if (empty($username)) {
            $errors[] = 'Please supply a valid username.';
            return false;
        } else {
            // Search for Existing Accounts by Username
            $check = $db->fetch("SELECT COUNT(`id`) AS `total` FROM `" . Auth::$table . "` WHERE "
                . "`username` = " . $db->quote($username)
                . (!empty($auth) ? " AND `id` != " . $db->quote($auth) : "")
            . " LIMIT 1;");

            // Require Unique Username
            if (!empty($check['total'])) {
                $errors[] = 'This username is already in use. Please try another.';
                return false;
            }
        }

        // Success
        return true;
    }

    /************* Authentication *************/

    /**
     * Get Auth Session
     * @uses static::validate
     * @return Auth
     */
    public static function get()
    {

        // Load from Session
        $auth = isset($_SESSION[static::AUTH_COOKIE]) ? $_SESSION[static::AUTH_COOKIE] : false;

        // New Auth User
        if (!$auth instanceof self) {
            $auth = new self ();
        }

        // Validate auth user
        if (!static::$validated) {
            static::$validated = true;
            $auth->validate();
        }

        // Save to Session & Return Auth
        return $_SESSION[static::AUTH_COOKIE] = $auth;
    }

    /**
     * Authenticate User in Database
     * @param string $username
     * @param string $password
     * @param DBInterface $db
     * @throws PDOException
     */
    public function authenticate($username, $password, DBInterface $db = null)
    {

        // DB Connection
        $db = !is_null($db) ? $db : DB::get();

        // Get hash to compare against
        $results = $db->fetch("SELECT `password` FROM `" . Auth::$table . "` WHERE `username` = " . $db->quote($username) . " LIMIT 1;");

        //User does not exist
        if (!$results) {
            $this->incrementLoginAttempts();
            return false;
        }

        //Incorrect Passwords (with REW exception)
        if (!(Settings::isREW() && empty($password) ) && !password_verify($password, $results['password'])) {
            $this->incrementLoginAttempts();
            return false;
        }

        //Set Username/Password
        $this->setUsername($username);
        $this->setPassword($results['password']);

        //Build/Set Authentication Token
        $this->_buildAuthToken();

        return true;
    }

    /**
     * Update an existing authentication with changed credentials
     * @param string $username
     * @param string $password
     * @param DB $db
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
     * Validate User in Database
     * @param DBInterface $db
     * @return bool
     * @throws PDOException
     */
    public function validate(DBInterface $db = null)
    {

        // DB Connection
        $db = !is_null($db) ? $db : DB::get();

        // Require Auth Token
        if (empty($this->token)) {
            $this->setValid(false);
            return false;
        }

        // Locate Auth Record
        $query = "SELECT `id`, `type`, `username`, `password`, `last_logon` FROM `" . static::$table . "` WHERE SHA1(CONCAT(UPPER(`username`), '" . $this->USER_PEPPER . "', `password`)) = '" . $this->token . "' LIMIT 1;";
        $this->auth = $db->fetch($query);
        if (!empty($this->auth)) {
            // Set Auth Type
            $this->setType($this->auth['type']);

            // Require Auth Table
            $table = $this->getTable();
            if (empty($table)) {
                $this->setValid(false);
                return false;
            }

            if (// Require ISA Module
                ($this->isAssociate() && empty(Settings::getInstance()->MODULES['REW_ISA_MODULE']))
                // Require Lender Module
                || ($this->isLender() && empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE']))
            ) {
                $this->setValid(false);
                return false;
            }

            // Locate Account Record
            $query = "SELECT `a`.*, `t`.`TZ` FROM `" . $table . "` `a` LEFT JOIN `timezones` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`auth` = '" . $this->auth['id'] . "' LIMIT 1;";
            $this->info = $db->fetch($query);
            if (!empty($this->info)) {
                // Merge Records
                $this->info = array_merge($this->auth, $this->info);

                // Parse JSON
                $json_fields = array('partners');
                foreach ($json_fields as $col) {
                    if (is_string($this->info[$col])) {
                        $json = json_decode($this->info[$col], true);
                        if (empty($json)) {
                            $json = array();
                        }
                        $this->info[$col] = $json;
                    }
                }

                // Auth ID
                $this->id = $this->info['auth'];

                // New login
                if (!$this->isValid()) {
                    // Update Login Timestamp
                    $db->query("UPDATE `" . static::$table . "` SET `last_logon` = NOW() WHERE `id` = '" . $this->id . "' LIMIT 1;");

                    // Set Valid
                    $this->setValid(true);

                    // Update Cookie
                    $this->_updateCookie();

                    // Log in to MoxieManager
                    $this->moxieManagerLogin();
                }

                // Success
                return true;
            } else {
                // Invalid
                $this->setValid(false);
            }
        } else {
            // Invalid
            $this->setValid(false);
        }

        // Log out of MoxieManager
        $this->moxieManagerLogin();

        // Invalid
        return false;
    }

    /**
     * Logout
     * @return void
     */
    public function logout()
    {

        // Clear Info
        $this->info = array();

        // Clear Details
        $this->setValid(false);
        $this->setCookie(false);
        $this->setUsername(false);
        $this->setPassword(false);
        $this->setToken(false);

        // Remove from Session
        unset($_SESSION[static::AUTH_COOKIE]);
        if (session_id() != '') {
            session_destroy();
        }
    }

    /**
     * Get Cookie Name
     * @return string
     */
    public static function getCookieName()
    {
        return static::AUTH_COOKIE;
    }

    private function _resolveDotNotation($source, $selector)
    {
        $parts = explode('.', $selector);
        foreach ($parts as $key) {
            $source = $source[$key];
        }
        return $source;
    }

    /**
     * @deprecated
     */
    public function is_super_admin()
    {
        return $this->isSuperAdmin();
    }

    /************* MoxieManager *************/

    /**
     * Only load moxie manager settings once
     * @var bool
     */
    private static $_moxieManagerLoaded;

    /**
     * Get Path to Uploads
     * @return string
     */
    public function getUploadPath()
    {
        $id = $this->info('id');
        if (!empty($id)) {
            $path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/agent-' . $id . '/'; // . $this->username;
            if (is_dir($path)) {
                @chmod($path, 02775);
                return $path;
            }
            if (@mkdir($path)) {
                @chmod($path, 02775);
                return $path;
            }
        }
        return null;
    }

    /**
     * Log in to MoxieManager
     */
    public function moxieManagerLogin()
    {
        if (!static::$_moxieManagerLoaded) {
            static::$_moxieManagerLoaded = true;
            $uploads = $this->getUploadPath();
            if (!empty($uploads)) {
                $_SESSION['isLoggedIn'] = true;
                unset($_SESSION['moxiemanager.filesystem.rootpath']);
                $share = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
                $_SESSION['moxiemanager.filesystem.rootpath'] = 'Your Files=' . $uploads
                    . ';Site Files=' . $share
                    . ';Documents=' . $_SERVER['DOCUMENT_ROOT'] . '/uploads/shared/documents/'
                    . ';Images=' . $_SERVER['DOCUMENT_ROOT'] . '/uploads/shared/images/'
                . ';';
                $_SESSION['moxiemanager.filesystem.directories'] = array(
                    'Site Files'    => array('filesystem.writable' => ($this->isSuperAdmin() || $this->info('mode') === 'admin'), 'filesystem.exclude_directory_pattern' => '/^(agent-[0-9]+|shared|thumb[s]*|\.svn)$/i'),
                    'Documents'     => array('filesystem.writable' => true, 'upload.extensions' => 'pdf,doc,docx,rtf,csv,xls,ppt,pptx,wpd,wps,pages,numbers,key'),
                    'Images'        => array('filesystem.writable' => true, 'upload.extensions' => 'jpg,jpeg,png,gif,psd,bmp,svg')
                );
                // MoxieManager Dropbox Integration
                try {
                    $dropboxAppId = Settings::get('moxiemanager.dropbox.app_id');
                    unset($_SESSION['moxiemanager.dropbox.app_id']);
                    if (!empty($dropboxAppId)) {
                        $_SESSION['moxiemanager.dropbox.app_id'] = $dropboxAppId;
                    }
                } catch (PDOException $e) {
                }
            }
        }
    }

    /**
     * Log out of MoxieManager
     */
    public function moxieManagerLogout()
    {
        $_SESSION['isLoggedIn'] = false;
        $_SESSION['moxiemanager.dropbox.app_id'] = false;
        $_SESSION['moxiemanager.filesystem.rootpath'] = false;
        $_SESSION['moxiemanager.filesystem.directories'] = false;
    }
}
