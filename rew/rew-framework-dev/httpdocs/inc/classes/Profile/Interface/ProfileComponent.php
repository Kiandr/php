<?php

/**
 * Profile_Interface_ProfileComponent
 * Objects implementing this interface can adjust their behavior based on a given profiling mode
 * @see Profile::PROFILE_MODE_PRODUCTION, Profile::PROFILE_MODE_DEVELOPMENT
 *
 */
interface Profile_Interface_ProfileComponent
{

    /**
     * Set the current profiling mode
     * @param int $mode
     */
    public function setProfileMode($mode);

    /**
     * Get the current profiling mode
     * @return int
     */
    public function getProfileMode();
}
