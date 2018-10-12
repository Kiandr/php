<?php

use REW\Core\Interfaces\UserInterface;

abstract class User implements UserInterface
{

    /**
     * Use Cookies
     * @var bool
     */
    protected $cookie;

    /**
     * Auth Token SHA1(Username+Pepper+Password)
     * @var string
     */
    protected $token;

    /**
     * Username
     * @var string
     */
    protected $username;

    /**
     * Password
     * @var string
     */
    protected $password;

    /**
     * Encryption Secret
     * @var string
     */
    protected $USER_PEPPER = 'Xemm4H2E1qmZaaorRK76';

    /**
     * Encryption cost
     * @var string
     */
    protected $USER_BCRYPT_COST = 10;

    /**
     * Failed Attempt Limit
     * @var string
     */
    protected $USER_RATE_LIMIT = 1000;

    /**
     * Failed Attempts timeframe
     * @var string
     */
    protected $USER_RATE_LIMIT_LENGTH = 1;

    /**
     * Too-Many_attempts ban length
     * @var string
     */
    protected $USER_BAN_LENGTH = 5;

    /**
     * Getter
     *
     * @link http://php.net/language.oop5.overloading
     * @param string $name
     * @return mixed
     */
    public function &__get($name)
    {
        $name = strtolower($name);

        switch ($name) {
            case 'USER_RATE_LIMIT':
                return $this->USER_RATE_LIMIT;
                break;
            case 'USER_RATE_LIMIT_LENGTH':
                return $this->USER_RATE_LIMIT_LENGTH;
                break;
            case 'USER_BAN_LENGTH':
                return $this->USER_BAN_LENGTH;
                break;
        }

        return null;
    }

    /**
     * Set Token
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Generates options for password_hash based on PASSWORD_DEFAULT algorithm
     * @return array
     */
    protected function getEncryptPasswordOptions()
    {

        //Generate Options
        $options = array();
        switch (PASSWORD_DEFAULT) {
            case PASSWORD_BCRYPT:
                $options['cost'] = $this->USER_BCRYPT_COST;
                break;
        }
        return $options;
    }

    /**
     * Build Authentication Token
     * @return void
     */
    protected function _buildAuthToken($token = null)
    {

        // Set Submitted Token
        if (!empty($token)) {
            $this->setToken($token);
        // Generate Token
        } else if (!empty($this->username) && !empty($this->password)) {
            //Build Encrypted Password
            $this->setToken(sha1(strtoupper($this->username) . $this->USER_PEPPER . $this->password));
        }

        // Update Cookie
        $this->_updateCookie();
    }

    /**
     * Set Cookie
     *
     * @param bool $cookie
     * @return void
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
        // Delete Old Cookies
        if (empty($this->cookie)) {
            $secure = Http_Uri::getScheme() === 'https' ? true : false;
            setcookie(static::getCookieName(), '', 0, '/', Http_Host::getCookieDomain(), $secure, 1);
        }
    }

    /**
     * Update Cookie with Authentication Token
     * @return void
     */
    protected function _updateCookie()
    {

        if ($this->cookie && !empty($this->token)) {
            $secure = Http_Uri::getScheme() === 'https' ? true : false;
            setcookie(static::getCookieName(), $this->token, strtotime('+30 day'), '/', Http_Host::getCookieDomain(), $secure, 1);
        }
    }

    /**
     * Load Authentication Token From Cookie
     * @return void
     */
    protected function _loadTokenFromCookie()
    {

        if (isset($_COOKIE[static::getCookieName()]) && !empty($_COOKIE[static::getCookieName()])) {
            $this->setCookie(true);
            $this->setToken($_COOKIE[static::getCookieName()]);
        }
    }

    /**
     * @return string
     */
    protected function _getCacheKeyForLoginAttempts()
    {
        return sha1(static::getCookieName() . ':' . 'Attempt:' . $_SERVER['REMOTE_ADDR']);
    }

    /**
     * Get # of login attempts
     * @return int
     */
    public function getLoginAttempts()
    {
        $cacheKey = $this->_getCacheKeyForLoginAttempts();
        $loginAttempts = Cache::getCache($cacheKey);
        return $loginAttempts ?: 0;
    }

    /**
     * Get # of remaining login attempts
     * @return int
     */
    public function getRemainingLoginAttempts()
    {
        $maxAttempts = $this->USER_RATE_LIMIT;
        $loginAttempts = $this->getLoginAttempts();
        $remainingAttempts = $maxAttempts - $loginAttempts;
        if ($remainingAttempts < 0) {
            return 0;
        }
        return $remainingAttempts;
    }

    /**
     * Set # of login attempts
     * @param int $attempts
     * @return $this
     */
    public function setLoginAttempts($attempts)
    {
        $cacheKey = $this->_getCacheKeyForLoginAttempts();
        Cache::setCache(
            $cacheKey,
            $attempts,
            false,
            ($this->getRemainingLoginAttempts() <= 0 ? $this->USER_BAN_LENGTH * 60 : $this->USER_RATE_LIMIT_LENGTH * 60)
        );
        return $this;
    }

    /**
     * Increment login attempts by 1
     */
    public function incrementLoginAttempts()
    {
        $attempts = $this->getLoginAttempts();
        $this->setLoginAttempts($attempts + 1);
    }

    /**
     * Generates a new encryped password
     * @param string
     * @return string
     */
    public function encryptPassword($password)
    {

        //Generate Password
        if ($encryptedPassword = password_hash($password, PASSWORD_DEFAULT, $this->getEncryptPasswordOptions())) {
            //Save Password and Salt
            return $encryptedPassword;
        } else {
            return null;
        }
    }
}
