<?php

/**
 * Profile_Trait_DebugUtils
 *
 */
trait Profile_Trait_DebugUtils
{

    /**
     * Cached snapshot of the stack
     * @var array
     */
    protected $_stack_snapshot;

    /**
     * Unique identifier
     * @var string
     */
    protected $_uid;

    /**
     * Arbitrary user-specified data
     * @var mixed
     */
    public $meta;

    /**
     * Get the unique identifier
     * @return string
     */
    public function getUID()
    {
        if (is_null($this->_uid)) {
            $this->_uid = hash('sha256', uniqid('', true));
        }
        return $this->_uid;
    }

    /**
     * Take a snapshot of the stack
     */
    protected function stackSnapshot()
    {
        $this->_stack_snapshot = $this->getStackTrace();
    }

    /**
     * Get the snapshotted stack
     * @return array
     */
    public function getStackSnapshot()
    {
        return $this->_stack_snapshot;
    }

    /**
     * Resolve and obtain the current stack trace
     * @return array
     */
    private function getStackTrace()
    {
        $stack_trace = array();

        // Known paths & classes
        $ignore_paths = array(realpath(dirname(__FILE__) . '/../'));

        // Parse stacktrace
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($stack as $trace) {
            $skip = false;
            if (!empty($trace['file'])) {
                foreach ($ignore_paths as $path) {
                    if (strpos($trace['file'], $path) === 0) {
                        $skip = true;
                        break;
                    }
                }
            }
            if ($skip) {
                continue;
            }

            // Add to collection
            $stack_trace[] = $trace;
        }

        return $stack_trace;
    }
}
