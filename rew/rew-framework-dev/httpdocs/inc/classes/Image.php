<?php

/**
 * Image
 *
 */
class Image
{

    /**
     * Error Code: Cannot Read File
     * @var int
     */
    const CANNOT_READ           = 1;

    /**
     * Error Code: Un-Supported MIME Type
     * @var int
     */
    const UNSUPPORTED_MIMETYPE  = 2;

    /**
     * Error Code: Re-Size Failure
     * @var int
     */
    const RESAMPLE_FAILED       = 3;

    /**
     * Error Code: Cannot Write File
     * @var int
     */
    const CANNOT_WRITE          = 4;

    /**
     * Error Code: Failed to Write
     * @var int
     */
    const WRITE_FAIL            = 5;

    /**
     * Error Code: Failed to as Image
     * @var int
     */
    const CANNOT_READ_STREAM    = 6;

    /**
     * Error Code: Not Enough Memory
     * @var int
     */
    const INSUFFICIENT_MEMORY   = 7;

    /**
     * Image Name
     * @var string
     */
    private $name;

    /**
     * Image Width
     * @var int
     */
    private $width;

    /**
     * Image Height
     * @var int
     */
    private $height;

    /**
     * Image Data / Stream
     * @var string
     */
    private $data;

    /**
     * Image MIME Type (eg: "image/gif")
     * @var string
     */
    private $mimetype;

    /**
     * Image Orientation
     * @var string
     */
    private $orientation;

    /**
     * Memory Limit
     * @var int
     */
    private $hardMemLimit;

    /**
     * Re-Sample Ratio
     * @var int
     */
    private $ratio;

    /**
     * Create New Image
     *
     * @param int $hardMemLimit    Memory Limit (in Bytes)
     * @param int $ratio           Re-Sample Ratio
     * @return void
     */
    public function __construct($hardMemLimit = 33554432, $ratio = 1)
    {
        $this->hardMemLimit = $hardMemLimit;
        $this->ratio = $ratio;
    }

    /**
     * Load Image from File
     *
     * @param string $file    Path to File
     * @return void
     * @throws Exception
     */
    public function readFile($file)
    {
        if (!is_readable($file)) {
            throw new Exception("File '$file' is not readable.  Does it exist?", self::CANNOT_READ);
        }

        list($this->width, $this->height, $type) = getimagesize($file);
        $this->mimetype = image_type_to_mime_type($type);
        if ($this->mimetype != "image/jpeg" && $this->mimetype != "image/gif" && $this->mimetype != "image/png") {
            throw new Exception("File type '{$this->mimetype}' is not supported.  Please convert to GIF, JPEG, or PNG.", self::UNSUPPORTED_MIMETYPE);
        }

        $exif = exif_read_data($file);
        if (isset($exif['Orientation'])) {
            $this->orientation = $exif['Orientation'];
        }

        $this->data = file_get_contents($file);
    }

    /**
     * Load Image from String
     *
     * @param string $stream      Image Data
     * @param int $width          Image Width
     * @param int $height         Image Height
     * @param string $mimetype    Image MIME Type (Must be Supported: image/jpeg, image/gif, image/png)
     * @return void
     * @throws Exception
     */
    public function readStream($stream, $width, $height, $mimetype)
    {
        if ($mimetype != "image/jpeg" && $mimetype != "image/gif" && $mimetype != "image/png") {
            throw new Exception("File type '$mimetype' is not supported.  Please convert to GIF, JPEG, or PNG.", self::UNSUPPORTED_MIMETYPE);
        }
        $this->data     = $stream;
        $this->width    = $width;
        $this->height   = $height;
        $this->mimetype = $mimetype;
    }

    /**
     * Write Image to File (Saves as JPEG)
     *
     * @param string $path     Path to File
     * @param int $quality     JPEG Quality
     * @return void
     * @throws Exception
     */
    public function write($path, $quality = 75)
    {
        $path = is_dir($path) ? rtrim($path, '/') . '/' . $this->getName() : $path;

        $stream = imagecreatefromstring($this->data);
        if (!$stream) {
            throw new Exception("Stream not recognized.", self::CANNOT_READ_STREAM);
        }

        if (!empty($this->orientation)) {
            switch ($this->orientation) {
                case 3:
                    $stream = imagerotate($stream, 180, 0);
                    break;
                case 6:
                    $stream = imagerotate($stream, -90, 0);
                    break;
                case 8:
                    $stream = imagerotate($stream, 90, 0);
                    break;
            }
        }

        // Enable interlacing (Progressive Images)
        imageinterlace($stream, true);
        if (!imagejpeg($stream, $path, $quality)) {
            throw new Exception("Cannot save JPEG of image stream.", self::WRITE_FAIL);
        }
        // Compress Image
        $mimeType = $this->getMimetype();
        if ($mimeType == 'image/jpeg') {
            // Use ImageMagick To Compress Image
            exec("convert -interlace line " . escapeshellarg($path) . " " . escapeshellarg($path));
        } else if ($mimeType == 'image/png') {
            // Use OptiPNG To Compress Image
            exec("optipng -backup " . escapeshellarg($path) . " -clobber " . escapeshellarg($path));
        }
    }

    /**
     * Re-Sample Image
     *
     * @param int $width         Resize Width
     * @param int $height        Resize Height
     * @param bool $keepRatio    Keep Image Ratio Intact
     * @return Image
     * @throws Exception
     */
    public function resample($width, $height, $keepRatio = true)
    {
        if ($keepRatio) {
            $this->calculateNewDimensions($width, $height, $newWidth, $newHeight);
        } else {
            $newWidth  = $width;
            $newHeight = $height;
        }
        // check if a resize is actually required
        if ($this->width != $newWidth || $this->height != $newHeight) {
            if (!$this->predict($newWidth, $newHeight)) {
                throw new Exception("Insufficient memory.", self::INSUFFICIENT_MEMORY);
            }
            $source = imagecreatefromstring($this->data);
            $destination = imagecreatetruecolor($newWidth, $newHeight);
            if (!imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height)) {
                throw new Exception("Resample operation failed.", self::RESAMPLE_FAILED);
            }
            // turn on progressive
            imageinterlace($destination, 1);
            $newImage = new Image;
            // use same image name
            if (!empty($this->name)) {
                $newImage->setName($this->name);
            }
            // capture new data stream
            ob_start();
            imagejpeg($destination, null, 100);
            $newImage->readStream(ob_get_clean(), $newWidth, $newHeight, 'image/jpeg');
            return $newImage;
        }
        return $this;
    }

    /**
     * Calculate New Dimensions
     *
     * @param int $newWidth        Desired Width
     * @param int $newHeight       Desired Height
     * @param int $outputWidth     Calculated Width
     * @param int $outputHeight    Calculated Height
     * @return bool
     */
    public function calculateNewDimensions($newWidth, $newHeight, &$outputWidth, &$outputHeight)
    {
        if ($this->width > $this->height) {
            $outputHeight = $this->height * $newWidth / $this->width;
            $outputWidth  = $newWidth;
        } elseif ($this->width < $this->height) {
            $outputWidth  = $this->width * $newHeight / $this->height;
            $outputHeight = $newHeight;
        } else {
            $outputWidth = $outputHeight = $newHeight;
        }
        return true;
    }

    /**
     * Get Image Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get Image Data
     *
     * @return string
     */
    public function getStream()
    {
        return $this->data;
    }

    /**
     * Get Image Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get Image Height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Get Image MIME Type
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set Memory Limit
     *
     * @param int $limit
     * @return void
     */
    public function setHardMemLimit($limit)
    {
        $this->hardMemLimit = $limit;
    }

    /**
     * Set Re-Sample Ratio
     *
     * @param int $ratio
     * @return void
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * Set Image Name
     *
     * @param string $name
     * @throws Exception If file extention is unknown
     */
    public function setName($name)
    {

        // Generate Filename
        $imageExt = substr(strrchr($name, '.'), 1);

        // Unknown File Extension
        if (empty($imageExt)) {
            $mimeType = $this->getMimetype();
            if ($mimeType == 'image/jpeg') {
                $imageExt = '.jpg';
            }
            if ($mimeType == 'image/gif') {
                $imageExt = '.gif';
            }
            if ($mimeType == 'image/png') {
                $imageExt = '.png';
            }
            if (empty($imageExt)) {
                throw new Exception('The image you are trying to upload does not have a file extension.');
            }
            // Append File Extension
            $this->name = rtrim($name, '.') . $imageExt;
        } else {
            // Set File Name
            $this->name = $name;
        }
    }

    /**
     * Predict Memory Limit
     *
     * @param int $targetWidth
     * @param int $targetHeight
     * @return bool
     */
    public function predict($targetWidth, $targetHeight)
    {
        $memoryRequired = 0;
        // check memory limit
        if (ini_get("memory_limit") !== '') {
            $memoryLimitDefault = ini_get('memory_limit');
            if (is_string($memoryLimitDefault)) {
                if (strrpos($memoryLimitDefault, 'M') == strlen($memoryLimitDefault) - 1) {
                    $memoryLimitDefault = substr($memoryLimitDefault, 0, strlen($memoryLimitDefault) - 1) * 1048576;
                } else {
                    settype($memoryLimitDefault, 'integer');
                }
            }
            $ratio = 3 / $this->ratio;
            if ($this->hardMemLimit > $memoryLimitDefault) {
                if (ini_set('memory_limit', $this->hardMemLimit)) {
                    $memoryLimit = $this->hardMemLimit;
                } else {
                    $memoryLimit = $memoryLimitDefault;
                }
            } else {
                $memoryLimit = $memoryLimitDefault;
            }
            $memoryRequired = $targetWidth * $targetHeight * $ratio + $this->width * $this->height * $ratio;
        }
        return ($memoryRequired <= $memoryLimit) ? true : false;
    }
}
