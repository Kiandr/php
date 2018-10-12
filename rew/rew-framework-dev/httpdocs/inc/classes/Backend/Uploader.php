<?php

use REW\Core\Interfaces\SettingsInterface;

/**
 * Backend_Uploader
 * @package Backend_Uploader
 */
abstract class Backend_Uploader
{

    /**
     * Allowed File Types
     * array_keys are extensions
     * array_values are mime types
     * @var array
     */
    protected $_allowedExtensions;

    /**
     * Maximum File Size
     * @var int
     */
    protected $_sizeLimit;

    /**
     * File Parameter
     * @var string
     */
    protected $_file;

    /**
     * File Extension
     * @var string
     */
    protected $_ext;

    /**
     * File Uploader type
     * @var string
     */
    protected $_uploader_type;

    /**
     * File Name
     * @var string
     */
    protected $name;

    /**
     * Get File Uploader
     * @return Backend_Uploader_Xhr|Backend_Uploader_Form
     */
    public static function get()
    {
        $handler = isset($_GET['qqfile']) ? 'Xhr' : 'Form';
        $className = __CLASS__ . '_' . $handler;
        return new $className;
    }

    /**
     * Setup Uploader
     * @param string $file File Parameter
     * @param string $file_type File Type parameter(ie. images, documents), found in app/config/defaults/files.php
     * @param string $file_subtype File format to change specific upload settings
     * @throws Exception
     */
    public function __construct($file = 'qqfile', $file_type = "default", $file_subtype = "default")
    {
        // Set uploader type used in validation
        $this->_uploader_type = $file_type;

        // Set uploader default configuration
        try {
            $files_config = Container::getInstance()->get(SettingsInterface::class)->FILES;
        } catch(\Exception $exception) {
            throw new Exception("Error initializing uploader.");
        }
        $this->_sizeLimit = $this->_toBytes(
            $files_config[$file_type][$file_subtype]['file_size_limit'] ?: $files_config['default']['file_size_limit']
        );
        $this->_allowedExtensions = $files_config[$file_type][$file_subtype]['allowed_extensions'] ?: $files_config['default']['allowed_extensions'];

        // Legacy defaults as override
        $this->_checkServerSettings();

        // Form id for file to upload
        $this->_file = $file;
    }

    /**
     * Get File Extension
     * @param boolean $reload True to reload
     * @return string
     */
    public function getExtension($reload = false)
    {
        if (is_null($this->_ext) || $reload) {
            $pathinfo = pathinfo($this->getName());
            $this->_ext = strtolower($pathinfo['extension']);
        }
        return $this->_ext;
    }

    /**
     * Get File Name
     * @return string
     */
    abstract public function getName();

    /**
     * Set File Name
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get File Size
     * @return int
     */
    abstract public function getSize();

    /**
     * Get File Type
     * @return string
     */
    abstract public function getType();

    /**
     * Read File Contents
     * @throws Exception If error occurs
     * @return string
     */
    abstract public function read();

    /**
     * Save File
     * @param string $file
     * @throws Exception If error occurs
     * @return true
     */
    abstract public function save($file);

    /**
     * Get Allowed File Extensions
     * @return array
     */
    public function getAllowedExtensions()
    {
        return array_keys($this->_allowedExtensions);
    }

    /**
     * Get Allowed File Mimes
     * @return array
     */
    public function getAllowedMimes()
    {
        return array_values($this->_allowedExtensions);
    }

    /**
     * Get Size Limit
     * @return int
     */
    public function getSizeLimit()
    {
        return $this->_sizeLimit;
    }

    /**
     * Set Allowed File Extensions
     * @param array $allowedExtensions
     * @return void
     */
    public function setAllowedExtensions(array $allowedExtensions = array())
    {
        $this->_allowedExtensions = array_map('strtolower', $allowedExtensions);
    }

    /**
     * Set Size Limit
     * @param mixed $sizeLimit - use an int for byte size or a string to convert from string to bytes
     * @example $this->setSizeLimit("600k");
     * @return void
     */
    public function setSizeLimit($sizeLimit)
    {
        if(is_string($sizeLimit)) {
            $this->_sizeLimit = $this->_toBytes($sizeLimit);
        } else if(is_integer($sizeLimit)) {
            $this->_sizeLimit = $sizeLimit;
        }
    }

    /**
     * Handle Upload Checks
     * @param string $path Save upload to directory
     * @param boolean $replace Replace existing files ($path must be set)
     * @throws Exception If error occurs
     * @return true
     */
    public function handleUpload($path = null, $replace = false)
    {

        // Require File
        if (!$this->_file) {
            throw new Exception('No file has been uploaded');
        }

        // Check File Size
        $size = $this->getSize();
        if ($size == 0) {
            throw new Exception('File is empty');
        }
        if ($size > $this->_sizeLimit) {
            throw new Exception('File is too large (Max ' . max(1, $this->_sizeLimit / 1024 / 1024) . 'MB)');
        }

        // Check File Extension
        $pathinfo = pathinfo($this->getName());
        $filename = isset($pathinfo['filename']) ? $pathinfo['filename'] : substr($pathinfo['basename'], 0, strrpos($pathinfo['basename'], '.'));
        $ext = $this->_ext = strtolower($pathinfo['extension']);

        // Check Mime type
        if(
            $this->_uploader_type == "images" &&
            !in_array(exif_imagetype($_FILES[$this->_file]['tmp_name']), $this->getAllowedMimes())
        ){

            throw new Exception('File has an invalid format, it should be one of these: ' . implode(', ', $this->getAllowedExtensions()));
        }

        // Check file extension
        if (!in_array($ext, $this->getAllowedExtensions())) {
            throw new Exception('File has an invalid extension, it should be one of these: ' . implode(', ', $this->getAllowedExtensions()));
        }

        // Save to Path
        if (!is_null($path)) {
            $path = rtrim($path, '/');

            // Require Writeable Path
            if (!is_writable($path)) {
                throw new Exception('Directory path is not writable.');
            }

            // Ensure Unique Filename
            if (empty($replace)) {
                while (file_exists($path . DIRECTORY_SEPARATOR . $filename . '.' . $ext)) {
                    $filename .= '.' . rand(10, 99);
                    $this->name = $filename . '.' . $ext;
                }
            }

            // Save File
            return $this->save($path . DIRECTORY_SEPARATOR . $this->name);
        }

        // Success
        return true;
    }

    /**
     * Check Server Settings, Update If Needed
     * @return void
     */
    protected function _checkServerSettings()
    {
        $postSize = $this->_toBytes(ini_get('post_max_size'));
        $uploadSize = $this->_toBytes(ini_get('upload_max_filesize'));
        if ($postSize < $this->_sizeLimit || $uploadSize < $this->_sizeLimit) {
            ini_set('post_max_size', $this->_sizeLimit);
            ini_set('upload_max_filesize', $this->_sizeLimit);
        }
    }

    /**
     * Convert String to Bytes
     * @param mixed $str
     * @return int
     */
    protected function _toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
}
