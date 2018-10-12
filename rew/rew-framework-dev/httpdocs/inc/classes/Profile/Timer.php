<?php

/**
 * Profile_Timer
 *
 */
class Profile_Timer implements Profile_Interface_NotificationHandler, Profile_Interface_ProfileComponent
{

    /**
     * Collection of stopwatches
     * @var array
     */
    protected $_stop_watches = array();

    /**
     * Current profiling mode
     * @var int
     */
    protected $_profile_mode = Profile::PROFILE_MODE_PRODUCTION;

    /**
     * Create or get an existing stopwatch instance
     * @param string $name
     * @param bool $strict_level Whether to only look for existing instances within the same nesting level
     * @return Profile_Timer_Stopwatch
     */
    public function getStopwatch($name, $strict_level = false)
    {

        // Already exists?
        $parent = $strict_level ? $this->getNewestRunningStopwatch($this->_stop_watches) : null;
        if ($existing = $this->getNamedStopwatch($name, $parent)) {
            return $existing;
        }

        // Create & return instance
        return $this->createStopwatch($name, $parent);
    }

    /**
     * Create or get an existing stopwatch instance
     * @param string $name
     * @param bool $strict_level Whether to look for existing instances only within the same nesting level
     * @see Profile_Timer::getStopwatch()
     * @return Profile_Timer_Stopwatch
     */
    public function stopwatch($name, $strict_level = true)
    {
        return $this->getStopwatch($name, $strict_level);
    }

    /**
     * Create a new stopwatch instance
     * @param string $name
     * @param Profile_Timer_Stopwatch $parent
     * @return Profile_Timer_Stopwatch
     */
    public function createStopwatch($name, Profile_Timer_Stopwatch $parent = null)
    {
        $watch = new Profile_Timer_Stopwatch($name);
        $watch->setProfileMode($this->getProfileMode());
        $watch->registerNotificationHandler($this);

        // Must be in dev mode
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $watch;
        }

        // Assign to parent
        if (is_null($parent)) {
            if ($parent = $this->getNewestRunningStopwatch($this->_stop_watches)) {
                $watch->setParent($parent);
            }
        } else {
            $watch->setParent($parent);
        }

        // Add to collection
        $this->_stop_watches[] = $watch;

        // Return stopwatch instance
        return $watch;
    }

    /**
     * Get the collection of top-level stopwatches
     * @return array
     */
    public function getStopwatches()
    {
        $watches = array();
        foreach ($this->_stop_watches as $watch) {
            if ($watch->getParent()) {
                continue;
            }
            $watches[] = $watch;
        }
        return $watches;
    }

    /**
     * Get the collection of stopwatches
     * @param array $collection
     * @param int $state Filter stopwatches by state
     * @return array
     */
    public function getOrderedStopwatchesInCollection($collection, $state = -1)
    {
        if (empty($collection)) {
            return array();
        }
        $watches = $collection;

        // Filter
        if ($state !== -1) {
            foreach ($watches as $k => $watch) {
                if ($watch->getState() !== $state) {
                    unset($watches[$k]);
                }
            }
        }

        // Sort by start time
        usort($watches, function ($a, $b) {
            $astart = $a->getInitialStartTime();
            $bstart = $b->getInitialStartTime();
            if ($astart == $bstart) {
                return 0;
            } else if ($astart > $bstart) {
                return 1;
            } else {
                return -1;
            }
        });
        return $watches;
    }

    /**
     * Handle a notification triggered by a notification emitter
     * @param int $type
     * @param mixed $sender
     * @param mixed $data
     */
    public function handleNotification($type, $sender, $data = null)
    {
        if ($sender instanceof Profile_Timer_Stopwatch) {
            switch ($type) {
                case Profile_Timer_Stopwatch::NOTIFICATION_TIMER_SUSPENDED:
                    // Re-assign children
                    if ($children = $sender->getChildren()) {
                        $parent = $sender->getParent();
                        foreach ($children as $child) {
                            // Child still running, but parent paused - re-assign child's parent to someone still paying attention
                            if ($child->isRunning()) {
                                // Set new parent
                                $child->setParent($parent);
                            }
                        }
                    }
                    break;
            }
        }
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

    /**
     * Get a stopwatch by name
     * @param string $name
     * @param Profile_Timer_Stopwatch $parent
     * @return Profile_Timer_Stopwatch|NULL
     */
    protected function getNamedStopwatch($name, Profile_Timer_Stopwatch $parent = null)
    {
        foreach ($this->_stop_watches as $watch) {
            if ($watch->getName() === $name) {
                if (!is_null($parent)) {
                    if ($watch->getParent() == $parent) {
                        return $watch;
                    }
                } else {
                    return $watch;
                }
            }
        }
        return null;
    }

    /**
     * Get the most recently started stopwatch that is still running
     * @param Profile_Timer_Stopwatch[] $collection
     * @return Profile_Timer_Stopwatch|NULL
     */
    protected function getNewestRunningStopwatch($collection)
    {
        $running_watches = $this->getOrderedStopwatchesInCollection($collection, Profile_Timer_Stopwatch::STOPWATCH_STATE_RUNNING);
        if (!empty($running_watches)) {
            $parent = array_pop($running_watches);
            return $parent;
        }
        return null;
    }
}
