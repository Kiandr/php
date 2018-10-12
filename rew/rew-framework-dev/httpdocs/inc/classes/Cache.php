<?php
use REW\Core\Interfaces\CacheInterface;

/**
 * Cache is used to handle all caching of files
 * @package REW
 */
class Cache implements CacheInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * @const int
     */
    const MODE_ON = 1;

    /**
     * @const int
     */
    const MODE_OFF = 0;

    /**
     * Cache Mode
     * @var int
     */
    protected $mode = self::MODE_ON;

    /**
     * File Location
     * @var string
     */
    protected $file;

    /**
     * Name of File
     * @var string
     */
    protected $name;

    /**
     * Path to File
     * @var string
     */
    protected $path;

    /**
     * Size of File
     * @var int
     */
    protected $size;

    /**
     * Enable File Locking when Reading / Writing
     * @var boolean
     */
    protected $lock = true;

    /**
     * Expire Time
     * @var int
     */
    protected $expires;

    /**
     * Memcache
     * @var Memcache
     */
    protected $memcache;

    /**
     * Get Memcache
     * @return Memcache|false
     */
    public function getMemcache()
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(CacheInterface::class, __FUNCTION__, func_get_args());
        }

        if (is_null($this->memcache)) {
            if (class_exists('Memcached')) {
                $this->memcache = new Memcached;
                if (@$this->memcache->addServer('localhost', 11211) === false) {
                    $this->memcache = false;
                }
            } else {
                $this->memcache = false;
            }
        }
        return $this->memcache;
    }

    /**
     * Get Cache
     * @param   string  $index  Cache Index
     * @param   bool    $server If true, $index affects all domains on localhost
     * @return mixed|NULL
     */
    public function getCache($index, $server = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(CacheInterface::class, __FUNCTION__, func_get_args());
        }

        $memcache = $this->getMemcache();
        if (!empty($memcache)) {
            // Prepend Domain
            if (empty($server)) {
                $index = Http_Host::getDomain() . ':' . $index;
            }
            // Get from Memcache
            $value = $memcache->get($index);
            if (!empty($value)) {
                return $value['data'];
            }
        }
        return null;
    }

    /**
     * Set Cache
     * @param   string  $index      Cache Index
     * @param   mixed   $value      Cache Data
     * @param   bool    $server     If true, $index affects all domains on localhost
     * @param   int     $duration   Set Duration Of Cache (In Seconds)
     * @return void
     */
    public function setCache($index, $value, $server = false, $duration = false)
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(CacheInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        $memcache = $this->getMemcache();
        if (!empty($memcache)) {
            // Prepend Domain
            if (empty($server)) {
                $index = Http_Host::getDomain() . ':' . $index;
            }
            // Set Duration
            if (empty($duration)) {
                $duration = (3600 + rand(0, 300));
            }
            // Save to Memcache
            $memcache->set($index, array('data' => $value), time() + $duration);
        }
    }

    /**
     * Delete Cache
     * @param   string  $index      Cache Index
     * @param   bool    $server     If true, $index affects all domains on localhost
     * @param   int     $duration   Time (In Seconds) until memcache will delete it.
     * @return void
     */
    public function deleteCache($index, $server = false, $duration = false)
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(CacheInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        $memcache = $this->getMemcache();
        if (!empty($memcache)) {
            // Prepend Domain
            if (empty($server)) {
                $index = Http_Host::getDomain() . ':' . $index;
            }
            // Set Duration
            if (empty($duration)) {
                $duration = 0;
            }
            // Save to Memcache
            $memcache->delete($index, $duration);
        }
    }

    /**
     * Get Cache Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Cache Path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get Expire Time
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Get Size of Cached File
     * @param bool $reload Force Reload
     * @return int
     */
    public function getSize($reload = false)
    {
        if (is_null($this->size) || $reload) {
            $filename = $this->path . $this->name;
            $this->size = filesize($filename);
        }
        return (int) $this->size;
    }

    /**
     * Setup Cache
     * @param array $options
     */
    public function __construct(array $options = array())
    {

        $options = array_merge(array(
            'path' => realpath(__DIR__ . '/../cache/') . DIRECTORY_SEPARATOR, // Cache Path
            'expires' => (60 * 60 * 24) // Expires in 24 Hours
        ), $options);
        // Set Options
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    /**
     * Set Cache Option
     * @var string $name name of the option
     * @var mixed $value value of the option
     * @access public
     */
    public function setOption($name, $value)
    {
        $availableOptions = array('file', 'name', 'path', 'lock', 'expires');
        if (in_array($name, $availableOptions)) {
            $this->$name = $value;
        }
    }

    /**
     * Save Data to Cache File
     *
     * @param string $id Cache ID (Filename)
     * @param string $data Data to Write
     * @return bool True on success, False on failure
     */
    public function save($id, $data)
    {
        // Cache Enabled
        if ($this->mode == self::MODE_ON) {
            // Set Filename
            $this->name = $id;
            // Write Data
            return $this->_write($data);
        }
    }

    /**
     * Write Cache
     * @param string $data
     * @return bool True on success, False on failure
     */
    protected function _write($data)
    {
        // Cache Enabled
        if ($this->mode == self::MODE_ON) {
            // Cache File
            $file = $this->path . $this->name;
            // Directory Level
            $paths = explode(DIRECTORY_SEPARATOR, $this->name);
            array_pop($paths);
            if (!empty($paths)) {
                $root = $this->path;
                foreach ($paths as $path) {
                    $root = $root . $path . DIRECTORY_SEPARATOR;
                    if (!(@is_dir($root))) {
                        @mkdir($root, 0700);
                    }
                }
            }
            // Already Cached
            if ($this->checkCache($this->name, $data)) {
                touch($file);
                return true;
            } else {
                // Write Data to File
                $fp = @fopen($file, 'wb');
                if ($fp) {
                    if ($this->lock) {
                        @flock($fp, LOCK_EX);
                    }
                    @fwrite($fp, $data);
                    if ($this->lock) {
                        @flock($fp, LOCK_UN);
                    }
                    @fclose($fp);
                    return true;
                }
                // Error Occurred
                return false;
            }
        }
    }

    /**
     * Read Cache
     */
    public function get()
    {
        return $this->_read();
    }

    /**
     * Read Cache
     * @return bool True on success, False on failure
     */
    protected function _read()
    {
        // Cache File
        $file = $this->path . $this->name;
        // Read File
        $fp = @fopen($file, 'rb');
        if ($this->lock) {
            @flock($fp, LOCK_SH);
        }
        if ($fp) {
            clearstatcache();
            $length = @filesize($file);
            $data = ($length) ? @fread($fp, $length) : '';
            if ($this->lock) {
                @flock($fp, LOCK_UN);
            }
            @fclose($fp);
            return $data;
        }
        // Error Occurred
        return false;
    }

    /**
     * Check Cache
     *
     * @param string $file File Name
     * @param string $data Check Contents
     */
    public function checkCache($file = null, $data = null)
    {
        if (!is_null($file)) {
            $this->name = $file;
        }
        $file = $this->getPath() . $this->name;
        if (is_file($file) && filesize($file) > 0) { // Check Exists
            if (is_null($data) || md5_file($file) == md5($data)) { // Check Contents
                return (time() - filemtime($file) < $this->getExpires()); // Check Modified
            }
        }
        return false;
    }
}
