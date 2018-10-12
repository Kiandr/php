<?php

/**
 * Profile
 *
 */
class Profile
{

    /**
     * Profiling mode - Production (OFF)
     * Objects returned by the profiler don't do any actual monitoring to improve performance
     * @var int
     */
    const PROFILE_MODE_PRODUCTION = 0;

    /**
     * Profiling mode - Development (ON)
     * Objects returned by the profiler perform monitoring & allow for a report to be generated
     * @var int
     */
    const PROFILE_MODE_DEVELOPMENT = 1;

    /**
     * Name of the default category for profile objects
     * @var string
     */
    const PROFILE_CATEGORY_GLOBAL = '_default';

    /**
     * Generic REW report - BREW
     * @var string
     */
    const REPORT_TYPE_GENERIC = 'Generic_REW';

    /**
     * UiKIT REW report - UiKIT
     * @var string
     */
    const REPORT_TYPE_UIKIT = 'UIKit_REW';

    /**
     * The report type to render
     * @var string
     */
    protected static $defaultReportType = self::REPORT_TYPE_GENERIC;

    /**
     * Sets the type of report to render
     * @param string $reportType
     */
    public static function setDefaultReportType($reportType)
    {
        self::$defaultReportType = $reportType;
    }

    /**
     * Collection of time profilers
     * @var Profile_Timer[]
     */
    protected static $_timer_profilers = array();

    /**
     * Collection of memory profilers
     * @var Profile_Memory[]
     */
    protected static $_memory_profilers = array();

    /**
     * Initial start time of the profiling session
     * @var float
     */
    protected static $_start_time;

    /**
     * End time of the profiling session
     * @var float
     */
    protected static $_duration;

    /**
     * Current profiling mode
     * @var int
     */
    protected static $_mode = self::PROFILE_MODE_PRODUCTION;

    /**
     * Begin a new profiling session
     */
    public static function startSession($mode = self::PROFILE_MODE_PRODUCTION)
    {
        self::endSession();
        self::$_mode = $mode;
        self::$_start_time = !empty($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        self::$_timer_profilers = array();
        self::$_memory_profilers = array();
    }

    /**
     * End the current profiling session
     */
    public static function endSession()
    {
        if (!empty(self::$_start_time)) {
            $now = microtime(true);
            self::$_duration = $now - self::$_start_time;
        }
    }

    /**
     * Get the current profiling mode
     * @return int
     */
    public static function getMode()
    {
        return self::$_mode;
    }

    /**
     * Get the time profiler instance
     * @param string $category
     * @return Profile_Timer
     */
    public static function timer($category = self::PROFILE_CATEGORY_GLOBAL)
    {
        if (!isset(self::$_timer_profilers[$category])) {
            self::$_timer_profilers[$category] = new Profile_Timer();
            if (self::$_timer_profilers[$category] instanceof Profile_Interface_ProfileComponent) {
                self::$_timer_profilers[$category]->setProfileMode(self::$_mode);
            }
        }
        return self::$_timer_profilers[$category];
    }

    /**
     * Get the memory profiler instance
     * @param string $category
     * @return Profile_Memory
     */
    public static function memory($category = self::PROFILE_CATEGORY_GLOBAL)
    {
        if (!isset(self::$_memory_profilers[$category])) {
            self::$_memory_profilers[$category] = new Profile_Memory();
            if (self::$_memory_profilers[$category] instanceof Profile_Interface_ProfileComponent) {
                self::$_memory_profilers[$category]->setProfileMode(self::$_mode);
            }
        }
        return self::$_memory_profilers[$category];
    }

    /**
     * Get report markup for the current profiling session
     * @param string $type Profile type
     * @param string $category
     * @return Profile_Report
     */
    public static function report($type = null, $category = self::PROFILE_CATEGORY_GLOBAL)
    {
        $type = $type ?: self::$defaultReportType;

        $class_name = 'Profile_Report_' . ucwords($type);
        if (!class_exists($class_name)) {
            return null;
        }

        // Calculate duration
        if (empty(self::$_duration)) {
            self::endSession();
        }

        // Pause stopwatches
        $watches = self::timer($category)->getStopwatches();
        foreach ($watches as $watch) {
            $watch->pauseAll();
        }

        // Create report instance
        $report = new $class_name(self::timer($category), self::memory($category), self::$_duration, self::$_start_time);
        if (!($report instanceof Profile_Report)) {
            return null;
        }
        return $report;
    }

    /**
     * Start over
     */
    public static function reset()
    {
        self::$_timer_profilers = array();
        self::$_memory_profilers = array();
        self::$_duration = null;
        self::$_start_time = null;
    }
}
