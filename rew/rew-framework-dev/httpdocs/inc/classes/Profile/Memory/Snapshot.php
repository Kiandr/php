<?php

/**
 * Profile_Memory_Snapshot
 *
 */
class Profile_Memory_Snapshot implements Profile_Interface_ProfileComponent
{
    use Profile_Trait_DebugUtils;

    /**
     * Current profiling mode
     * @var int
     */
    protected $_profile_mode = Profile::PROFILE_MODE_PRODUCTION;

    /**
     * The name/identifier for the snapshot
     * @var string
     */
    protected $_name;

    /**
     * Additional information associated with the snapshot
     * @var string
     */
    protected $_details;

    /**
     * Memory usage (in bytes) for the snapshot
     * @var int
     */
    protected $_usage;

    /**
     * Time of the snapshot
     * @var float
     */
    protected $_time;

    /**
     * Create a new memory snapshot instance
     * @param string $name
     */
    public function __construct($name = 'Memory Snapshot')
    {
        $this->_name = $name;
    }

    /**
     * Generate a snapshot
     * @return Profile_Memory_Snapshot
     */
    public function generate()
    {
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $this;
        }

        $this->_usage = memory_get_usage(true);
        $this->_time = microtime(true);

        // Take stack snapshot
        $this->stackSnapshot();
        return $this;
    }

    /**
     * Get the name of the stopwatch
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the stopwatch details
     * @return string|NULL
     */
    public function getDetails()
    {
        return $this->_details;
    }

    /**
     * Set the stopwatch details
     * @param string $details
     * @return Profile_Memory_Snapshot
     */
    public function setDetails($details)
    {
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $this;
        }
        $this->_details = $details;
        return $this;
    }

    /**
     * Get the time of the snapshot
     * @return float
     */
    public function getTime()
    {
        return $this->_time;
    }

    /**
     * Get the memory usage (in bytes) for the snapshot
     * @return int
     */
    public function getUsage()
    {
        return $this->_usage;
    }

    /**
     * Set the current profiling mode
     * @param int $mode
     */
    public function setProfileMode($mode)
    {
        $this->_profile_mode = $mode;
    }

    /**
     * Get the current profiling mode
     * @return int
     */
    public function getProfileMode()
    {
        return $this->_profile_mode;
    }
}
