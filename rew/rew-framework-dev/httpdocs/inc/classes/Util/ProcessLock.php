<?php

/**
 * Cron utility to prevent multiple instances of a cron job from running.
 * @author David
 *
 */
class Util_ProcessLock
{

    /**
     * Path to lock file.
     * @var string
     */
    private static $lock_file;

    private static $is_locked = false;

    /**
     * Attempts to create a lock using this process' ID.
     * Returns true if a lock has been created and false if a lock already exists
     * @return boolean
     */
    private static function tryLock()
    {
        // Attempts To Create A "lock" If The Symlink Is Created, Then A Lock Has Successfully Been Created
        // And The Method Will Return
        if (@symlink("/proc/" . getmypid(), self::$lock_file) !== false) {
            return true;
        }

        // The "lock" Already Exists, Check If It's Stale
        if (is_link(self::$lock_file) && !is_dir(self::$lock_file)) {
            // Remove The Old Lock
            self::unlock();

            // Try To Lock Again
            return self::tryLock();
        }

        return false;
    }

    /**
     * Set lock file path (passing __FILE__ is sufficient)
     * @param string $script_path
     */
    public static function setLockFile($script_path)
    {
        if (empty($script_path)) {
            throw new BadMethodCallException("The script path cannot be empty");
        }

        self::$lock_file = $script_path . ".lock";
    }

    /**
     * Returns true if locked and false if not (creates a lock in the process if false is returned)
     * @return boolean
     */
    public static function isLocked()
    {
        self::$is_locked = !self::tryLock();
        return self::$is_locked;
    }

    /**
     * Delete lock file.
     */
    public static function unlock()
    {
        unlink(self::$lock_file);
    }
}
