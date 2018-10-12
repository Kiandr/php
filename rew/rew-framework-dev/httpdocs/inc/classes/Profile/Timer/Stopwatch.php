<?php

/**
 * Profile_Timer_Stopwatch
 *
 */
class Profile_Timer_Stopwatch implements Profile_Interface_ProfileComponent
{
    use Profile_Trait_DebugUtils;
    use Profile_Trait_NotificationEmitter;
    use Profile_Trait_Adoptable;
    use Profile_Trait_Parent;

    /**
     * Stopwatch state - suspended, not incrementing elapsed time
     * @var int
     */
    const STOPWATCH_STATE_SUSPENDED = 0;

    /**
     * Stopwatch state - running & counting elapsed time
     * @var int
     */
    const STOPWATCH_STATE_RUNNING = 1;

    /**
     * Stopwatch notification - Stopwatch paused
     * @var int
     */
    const NOTIFICATION_TIMER_SUSPENDED = 0;

    /**
     * Stopwatch notification - Stopwatch resumed
     * @var int
     */
    const NOTIFICATION_TIMER_RESUMED = 1;

    /**
     * Current profiling mode
     * @var int
     */
    protected $_profile_mode = Profile::PROFILE_MODE_PRODUCTION;

    /**
     * The name/identifier for the stopwatch
     * @var string
     */
    protected $_name;

    /**
     * Additional information associated with the stopwatch
     * @var string
     */
    protected $_details;

    /**
     * Current state
     * @var int
     */
    protected $_state = self::STOPWATCH_STATE_SUSPENDED;

    /**
     * Time at which the stopwatch was first resumed
     * @var float
     */
    protected $_initial_start_time = 0;

    /**
     * Time at which the stopwatch was last resumed
     * @var float
     */
    protected $_start_time = 0;

    /**
     * Total duration of the stopwatch
     * @var float
     */
    protected $_elapsed_time = 0;

    /**
     * Collection of individual run iterations for the stopwatch
     * @var array
     */
    protected $_iterations = array();

    /**
     * Create a new stopwatch
     * @param string $name
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Resume the stopwatch
     * @return Profile_Timer_Stopwatch
     */
    public function resume()
    {
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $this;
        }
        $this->setState(self::STOPWATCH_STATE_RUNNING);
        return $this;
    }

    /**
     * Resume the stopwatch
     * @see Profile_Timer_Stopwatch::resume()
     * @return Profile_Timer_Stopwatch
     */
    public function start()
    {
        return $this->resume();
    }

    /**
     * Pause the stopwatch
     * @return Profile_Timer_Stopwatch
     */
    public function pause()
    {
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $this;
        }
        $this->setState(self::STOPWATCH_STATE_SUSPENDED);
        return $this;
    }

    /**
     * Pause the stopwatch
     * @see Profile_Timer::pause()
     * @return Profile_Timer_Stopwatch
     */
    public function stop()
    {
        return $this->pause();
    }

    /**
     * Pause the stopwatch along with all of its children recursively
     * @return Profile_Timer_Stopwatch
     */
    public function pauseAll()
    {

        // No children?
        if (!($children = $this->getChildren())) {
            $this->pause();
            return $this;
        }

        // Pause children first
        foreach ($children as $child) {
            $child->pauseAll();
        }

        // Pause self
        $this->pause();

        return $this;
    }

    /**
     * Pause the stopwatch along with all of its children recursively
     * @see Profile_Timer_Stopwatch::pauseAll()
     * @return Profile_Timer_Stopwatch
     */
    public function stopAll()
    {
        return $this->pauseAll();
    }

    /**
     * Pause the stopwatch & reset its elapsed time
     * @return Profile_Timer_Stopwatch
     */
    public function reset()
    {
        $this->pause();
        $this->_elapsed_time = 0;
        return $this;
    }

    /**
     * Get the total running time
     * @return number
     */
    public function getElapsedTime()
    {

        // Currently running - get up-to-date duration
        if ($this->_state == self::STOPWATCH_STATE_RUNNING && !empty($this->_start_time)) {
            $elapsed = microtime(true) - $this->_start_time;
            return $elapsed + $this->_elapsed_time;
        }

        return $this->_elapsed_time;
    }

    /**
     * Get the total running time
     * @see Profile_Timer_Stopwatch::getElapsedTime()
     * @return number
     */
    public function getDuration()
    {
        return $this->getElapsedTime();
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
     * Set the name of the stopwatch
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Get the stopwatch details
     * @return string|NULL
     */
    public function getDetails()
    {
        if ($this->getIterationCount() > 1) {
            return null; // Details available under each iteration instead
        }
        return $this->_details;
    }

    /**
     * Set the stopwatch details
     * @param string $details
     * @return Profile_Timer_Stopwatch
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
     * Append details to stopwatch
     * @param string $details
     * @return Profile_Timer_Stopwatch
     */
    public function appendDetails($details)
    {
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $this;
        }
        $this->_details .= $details;
        return $this;
    }

    public function clearDetails()
    {
        if ($this->getProfileMode() !== Profile::PROFILE_MODE_DEVELOPMENT) {
            return $this;
        }
        $this->_details = null;
        return $this;
    }

    /**
     * Get the individual iterations of the stopwatch
     * @return array
     */
    public function getIterations()
    {
        return $this->_iterations;
    }

    /**
     * Get the number of iterations
     * @return int
     */
    public function getIterationCount()
    {
        return count($this->_iterations);
    }

    /**
     * Get the time at which the stopwatch was first resumed
     * @return float
     */
    public function getInitialStartTime()
    {
        return $this->_initial_start_time;
    }

    /**
     * Check if the stopwatch is currently running
     * @return boolean
     */
    public function isRunning()
    {
        return $this->_state === self::STOPWATCH_STATE_RUNNING;
    }

    /**
     * Get the current state of the stopwatch
     * @return int
     */
    public function getState()
    {
        return $this->_state;
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
     * Set the current stopwatch state
     * @param int $state
     * @return Profile_Timer_Stopwatch
     */
    protected function setState($state)
    {
        if ($state === $this->_state) {
            return;
        }
        $this->_state = $state;
        switch ($this->_state) {
            case self::STOPWATCH_STATE_RUNNING:
                $now = microtime(true);
                $this->_start_time = $now;

                // Remember initial start
                if (empty($this->_initial_start_time)) {
                    $this->_initial_start_time = $now;
                }

                // Take stack snapshot
                $this->stackSnapshot();

                // Post notification
                $this->postNotification(self::NOTIFICATION_TIMER_RESUMED);

                break;
            case self::STOPWATCH_STATE_SUSPENDED:
                // Require start time
                if (empty($this->_start_time)) {
                    break;
                }

                // Calculate duration
                $now = microtime(true);
                $duration = $now - $this->_start_time;

                // Increment total
                $this->_elapsed_time += $duration;

                // Save iteration
                $this->_iterations[] = array(
                    'start' => $this->_start_time,
                    'end' => $now,
                    'duration' => $duration,
                    'details' => $this->_details,
                    'stack' => $this->getStackSnapshot()
                );

                // Post notification
                $this->postNotification(self::NOTIFICATION_TIMER_SUSPENDED);

                break;
        }
        return $this;
    }
}
