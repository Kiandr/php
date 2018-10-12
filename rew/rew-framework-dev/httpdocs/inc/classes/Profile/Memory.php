<?php

/**
 * Profile_Memory
 *
 */
class Profile_Memory implements Profile_Interface_ProfileComponent
{

    /**
     * Memory snapshots
     * @var array
     */
    protected $_memory_snapshots = array();

    /**
     * Current profiling mode
     * @var int
     */
    protected $_profile_mode = Profile::PROFILE_MODE_PRODUCTION;

    /**
     * Create a new memory snapshot instance
     * @param string $name
     * @return Profile_Memory_Snapshot
     */
    public function snapshot($name = 'Memory Snapshot')
    {
        $snapshot = new Profile_Memory_Snapshot($name);
        $snapshot->setProfileMode($this->getProfileMode());

        // Must be in dev mode
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $snapshot;
        }

        $snapshot->generate();
        $this->_memory_snapshots[] = $snapshot;
        return $snapshot;
    }

    /**
     * Get the memory snapshots
     * @return array
     */
    public function getSnapshots()
    {
        return $this->_memory_snapshots;
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
