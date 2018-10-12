<?php

namespace REW;

/**
 * Class Session
 * @package REW
 *
 * Simple class to start a session if a session should be started.
 */
class Session
{
    /**
     * Starts the session if the client is not running from the CLI
     */
    public function startSession()
    {
        if (!$this->isClientCLI() && !$this->isClientSessionStarted()) {
            session_start();
        }
    }

    /**
     * Checks if the session has been started. Returns true if so.
     *
     * @return bool
     */
    public function isClientSessionStarted()
    {
        return session_status() != PHP_SESSION_NONE;
    }

    /**
     * Checks if the client is running from the CLI. Returns true if so.
     *
     * @return bool
     */
    public function isClientCLI()
    {
        return php_sapi_name() == 'cli';
    }
}
