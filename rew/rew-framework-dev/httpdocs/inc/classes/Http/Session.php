<?php

/**
 * Http_Session
 *
 * Object-oriented Wrapper for $_SESSION
 *
 */
class Http_Session
{

    /**
     * Get Value
     *
     * @param string $key    Session Key
     * @return null|mixed
     * @global $_SESSION
     */
    public function __get($key)
    {
        global $_SESSION;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Set Value
     *
     * @param string $key     Session Key
     * @param mixed $value    Session Value
     * @return mixed
     * @global $_SESSION
     */
    public function __set($key, $value)
    {
        global $_SESSION;
        $_SESSION[$key] = $value;
        return $value;
    }

    /**
     * Unset Value
     *
     * @param string $key    Session Key
     * @return void
     * @global $_SESSION
     */
    public function __unset($key)
    {
        global $_SESSION;
        unset($_SESSION[$key]);
    }

    /**
     * Check Value
     *
     * @param string $key    Session Key
     * @return bool
     * @global $_SESSION
     */
    public function __isset($key)
    {
        global $_SESSION;
        return isset($_SESSION[$key]);
    }
}
