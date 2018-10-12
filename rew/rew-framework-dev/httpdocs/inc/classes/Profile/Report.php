<?php

/**
 * Profile_Report
 *
 */
abstract class Profile_Report
{

    /**
     * Time profiler instance
     * @var Profile_Timer
     */
    protected $_profile_timer;

    /**
     * Memory profiler instance
     * @var Profile_Memory
     */
    protected $_profile_memory;

    /**
     * Total duration of the profiling session
     * @var float|NULL
     */
    protected $_total_duration;

    /**
     * Time at which the profiling session began
     * @var float|NULL
     */
    protected $_start_time;

    /**
     * Get & return the report's markup
     * @return string
     */
    abstract public function getHTML();

    /**
     * Create a new profile report
     * @param Profile_Timer $timer
     * @param Profile_Memory $memory
     * @param float $total_duration
     * @param float $start_time
     */
    public function __construct(Profile_Timer $timer = null, Profile_Memory $memory = null, $total_duration = null, $start_time = null)
    {
        $this->_profile_timer = $timer;
        $this->_profile_memory = $memory;
        $this->_total_duration = $total_duration;
        $this->_start_time = $start_time;
    }

    /**
     * Set the report's time profiler
     * @param Profile_Timer $timer
     */
    public function setTimeProfiler(Profile_Timer $timer)
    {
        $this->_profile_timer = $timer;
    }

    /**
     * Set the report's memory profiler
     * @param Profile_Memory $memory
     */
    public function setMemoryProfiler(Profile_Memory $memory)
    {
        $this->_profile_memory = $memory;
    }

    /**
     * Get the time profiler's stopwatches in a chronological order
     * @return array
     */
    public function getOrderedStopwatches()
    {
        if (!($timer = $this->_profile_timer)) {
            return array();
        }
        return $timer->getOrderedStopwatchesInCollection($timer->getStopwatches());
    }

    /**
     * Get the memory profiler's snapshots in chronological order
     * @return array
     */
    public function getOrderedMemorySnapshots()
    {
        if (!($memory = $this->_profile_memory)) {
            return array();
        }
        $snapshots = $memory->getSnapshots();
        usort($snapshots, function ($a, $b) {
            $astart = $a->getTime();
            $bstart = $b->getTime();
            if ($astart == $bstart) {
                return 0;
            } else if ($astart > $bstart) {
                return 1;
            } else {
                return -1;
            }
        });
        return $snapshots;
    }

    /**
     * Sort an associative array by a given key
     * @param array $array
     * @param string $key
     * @param int $sort_order
     */
    protected function sortArrayWithKey(&$array, $key, $sort_order = SORT_ASC)
    {
        $key_values = array();
        foreach ($array as $k => $v) {
            $key_values[$k] = $v[$key];
        }
        array_multisort($key_values, $sort_order, $array);
    }

    /**
     * Convert bytes to a human-readable format
     * @param int $bytes
     * @return string
     */
    public function bytesToString($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    /**
     * Convert string to bytes
     * @param string $string
     * @return int
     */
    protected function stringToBytes($string)
    {
        $val = trim($string);
        $last = strtolower($val[strlen($val)-1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return intval($val);
    }
}
