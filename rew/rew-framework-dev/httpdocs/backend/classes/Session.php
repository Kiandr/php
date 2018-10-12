<?php

namespace REW\Backend;

use REW\Backend\Interfaces\SessionInterface;

class Session implements SessionInterface
{

    /**
     * Starts a session if one hasn't already been started
     * @param \REW\Session $session
     */
    public function __construct(\REW\Session $session)
    {
        $session->startSession();
    }

    /**
     * Writes and closes session
     * @return void
     */
    public function close()
    {
        session_write_close();
    }

    /**
     * Destroys session
     * @return boolean
     */
    public function destroy()
    {
        return session_destroy();
    }

    /**
     * Get Session Value
     * @param string $index
     * @return mixed
     */
    public function get($index)
    {
        return $_SESSION[$index];
    }

    /**
     * Set Session Value
     * @param string $index
     * @param mixed $value
     * @return void
     */
    public function set($index, $value)
    {
        $_SESSION[$index] = $value;
    }
}
