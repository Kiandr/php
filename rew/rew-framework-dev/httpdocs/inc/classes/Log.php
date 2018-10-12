<?php

use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SettingsInterface;

/**
 * App Log
 * @example Log::error('Error Occurred!')
 * @example Log::debug('Debug Information Here');
 * @example Log::db('Search Query', $query);
 *
 */
class Log implements LogInterface
{
    use REW\Traits\StaticNotStaticTrait;

    private $settings;

    /**
     * Log constructor.
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Log Mode
     * @var int
     */
    protected $mode;

    /**
     * Logged Information
     * @var array[]
     */
    protected $info = array();

    /**
     * Start Time
     * @var $start
     */
    protected $start = null;

    /**
     * Set Log Mode
     *
     * @param int $mode Log::MODE_ON or Log::MODE_OFF
     */
    public function setMode($mode)
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        switch ($mode) {
            case static::MODE_ON:
            case static::MODE_OFF:
                $this->mode = $mode;
                break;
        }
    }

    /**
     * Get Log Mode
     *
     * @return int
     */
    public function getMode()
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
        }

        return $this->mode;
    }

    /**
     * Get Log Info
     *
     * @return array
     */
    public function getInfo()
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
        }

        return $this->info;
    }

    /**
     * Halt
     *
     * @param string $error    Error Message
     * @param int $type        HTTP Status Code (503)
     * @return void
     */
    public function halt($error, $type = 503)
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        switch ($type) {
            case 503:
                header('HTTP/1.1 503 Service Unavailable');
                break;
        }
        $error = is_array($error) ? $error : array($error);
        foreach ($error as $err) {
            error_log('ERROR: '.$err);
        }
        $error = '<h3>Debug Info:</h3><ul><li>' . implode('</li><li>', $error) . '</li></ul>';
        $file = $this->settings['DIRS']['CACHE'] . 'html/error.html';
        if (file_exists($file)) {
            $html = file_get_contents($file);
            if (strpos($html, '{error}') !== false) {
                echo str_replace('{error}', $error, $html);
            } else {
                echo $error;
            }
        } else {
            echo $error;
        }
        exit;
    }

    /**
     * Log Error
     *
     * @return void
     */
    public function error()
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        $args = func_get_args() ;
        $this->log(static::ERROR, count($args) == 1 ? $args[0] : $args);
    }

    /**
     * Log Debug
     *
     * @return void
     */
    public function debug()
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        $args = func_get_args() ;
        $this->log(static::DEBUG, count($args) == 1 ? $args[0] : $args);
    }

    /**
     * Log Database
     *
     * @return void
     */
    public function db()
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        $args = func_get_args() ;
        $this->log(static::DATABASE, count($args) == 1 ? $args[0] : $args);
    }

    /**
     * Log Data
     *
     * @param string $type
     * @param mixed $data
     * @return void
     * @throws Exception on invalid log type
     */
    public function log($type, $data)
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        // Logging is Turned Off
        if ($this->mode == static::MODE_OFF) {
            return;
        }

        // Debug Trace..
        //$trace = debug_backtrace();
        //$trace = isset($trace[1]) ? $trace[1] : false;
        //$trace = 'Line #' . $trace['line'] . ': ' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $trace['file']);

        // Add Log
        switch ($type) {
            case static::ERROR:
            case static::DEBUG:
            case static::DATABASE:
                $this->info[$type][] = $data;
                break;
            default:
                throw new Exception('Unknown Log Type: ' . $type);
                break;
        }
    }

    /**
     * Display Log
     *
     * @return void
     */
    public function display()
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        // Output
        echo '<div id="rew-debug-info-togglr" style="position: absolute; top: 0; right: 0; z-index: 99999999;max-width:80%;"><div style="padding:5px 10px;background:#ccc;color:#000;font-weight:bold;">Debug Information <a href="javascript:void(0);" onclick="document.getElementById(\'rew-debug-info\').style.display = \'block\';">Show</a> / <a href="javascript:void(0);" onclick="document.getElementById(\'rew-debug-info\').style.display = \'none\';">Hide</a></div>';
        echo '<div id="rew-debug-info" style="width: 100%; display: none;">';
        if (!empty($this->info)) {
            echo '<table border="1" cellspacing="0" cellpadding="5" width="100%; margin: 0;">';
            foreach ($this->info as $type => $entries) {
                // Output
                echo '<tr class="rew-debug-info-ctrl">';
                echo '<th style="text-align:left;cursor:pointer;padding:5px;background:#fff;color:#000;border-bottom:1px solid #ccc;font-weight: bold;" colspan="2">' . strtoupper($type) . '</th>';
                echo '</tr>';

                // Display Log
                foreach ($entries as $entry) {
                    if (is_array($entry)) {
                        echo '<tr class="rew-debug-info-box">';
                        $rc = 1;
                        foreach ($entry as $v) {
                            $v = $v instanceof Exception ? $v->getMessage() . PHP_EOL . $v->getTraceAsString() : $v;
                            $rc = $rc == 1 ? 2 : 1;
                            echo '<td style="padding:5px;background:#fff;color:#000;border-bottom:1px solid #ccc;">';
                            echo '<pre'.($rc == 1 ? ' style="background:#eee;border-top:1px solid #ccc;"' : '').'>';
                            echo is_array($v) ? print_r($v, true) : $v;
                            echo '</pre>';
                            echo '</td>';
                        }
                        echo '</tr>';
                    } else {
                        $entry = $entry instanceof Exception ?  $entry->getMessage() . PHP_EOL . $entry->getTraceAsString() : $entry;
                        echo '<tr class="rew-debug-info-box">';
                        echo '<td style="padding:5px;background:#fff;color:#000;border-bottom:1px solid #ccc;" colspan="2"><pre>' . $entry . '</pre></td>';
                        echo '</tr>';
                    }
                }
            }
            echo '</table>';
        } else {
            echo '<p style="padding:5px;background-color:#fff;color:#000;">No Information to Display</p>';
        }
        echo '</div></div>';
    }

    /**
     * Stop Watch Debug
     *
     * @param int $start UNIX Timestamp
     * @return string
     */
    public function stopWatch($start = null)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(LogInterface::class, __FUNCTION__, func_get_args());
        }

        if (is_null($start)) {
            if (is_null($this->start)) {
                $this->start = microtime(true);
            }
            $start = $this->start;
        }
        $runTime = microtime(true) - $start;
        $hours    = floor($runTime / 3600);
        $runTime -= ($hours * 3600);
        $minutes  = floor($runTime / 60);
        $runTime -= ($minutes * 60);
        $seconds  = round($runTime, 2);
        return 'Running time: ' . $hours . ' hrs, ' . $minutes . ' mins, ' . $seconds . ' secs  | Memory Usage: ' . number_format(round((memory_get_usage() / 1024), 2)) . 'KB';
    }
}
