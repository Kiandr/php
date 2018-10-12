<?php

/**
 * Source_File extends Source and is used for Including Source Code from Local Files
 * @package REW
 * @subpackage Source
 */
class Source_File extends Source
{

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
     * File Contents
     * @var string
     */
    protected $data;

    /**
     * File Exists
     * @var bool
     */
    protected $exists;

    /**
     * File Extension
     * @var string
     */
    protected $extension;

    /**
     * Create File
     * @param string $file File Location
     * @param string $type
     */
    public function __construct($file, $type, $load = "none")
    {
        $this->file = $file;
        $this->load = $load;
        parent::__construct($type);
    }

    /**
     * Get File Location
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get File Extension
     * @param bool $reload Force Reload
     * @return string
     */
    public function getExtension($reload = false)
    {
        if (is_null($this->extension) || $reload) {
            $this->extension = pathinfo($this->file, PATHINFO_EXTENSION);
        }
        return $this->extension;
    }

    /**
     * Get Name of File
     * @param bool $reload Force Reload
     * @return string
     */
    public function getName($reload = false)
    {
        if (is_null($this->name) || $reload) {
            $this->name = pathinfo($this->file, PATHINFO_BASENAME);
        }
        return $this->name;
    }

    /**
     * Get Path to File
     * @param bool $reload Force Reload
     * @return string
     */
    public function getPath($reload = false)
    {
        if (is_null($this->path) || $reload) {
            $this->path = pathinfo($this->file, PATHINFO_DIRNAME);
        }
        return $this->path;
    }

    /**
     * Get Size of File
     * @param bool $reload Force Reload
     * @return int
     */
    public function getSize($reload = false)
    {
        if (is_null($this->size) || $reload) {
            $this->size = filesize($this->file);
        }
        return (int) $this->size;
    }

    /**
     * Get File Contents
     * @param bool $reload Force Reload
     * @return string
     */
    public function getData($reload = false)
    {
        if (is_null($this->data) || $reload) {
            if ($this->checkExists()) {
                ob_start();
                include $this->file;
                $this->data = ob_get_clean();
            }
        }
        return $this->data;
    }

    /**
     * Check If File Exist
     * @param bool $reload Force Reload
     * @return bool
     */
    public function checkExists($reload = false)
    {
        if (is_null($this->exists) || $reload) {
            $this->exists = realpath($this->file) ? true : false;
        }
        return $this->exists;
    }

    /**
     * Check if file is minified (by checking extension for ".min")
     * @param string $reload
     * @return bool
     */
    public function isMinified($reload = false)
    {
        if (is_null($this->minified) || $reload) {
            $extension = $this->getExtension();
            if ($extension === 'less') {
                $this->minified = false;
            } else {
                $min_extension = '.min.' . $extension;
                $this->minified = $min_extension === substr($this->file, -1 * abs(strlen($min_extension)));
            }
        }
        return $this->minified;
    }
}
