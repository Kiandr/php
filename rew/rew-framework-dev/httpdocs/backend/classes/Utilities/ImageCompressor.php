<?php

namespace REW\Backend\Utilities;

/**
 * ImageCompressor
 * @package ImageCompressor
 */
class ImageCompressor
{

    const COMPRESSABLE = ['jpg', 'jpeg', 'png'];

    const JPEG_EXTS    = ['jpg', 'jpeg'];

    private $path;

    private $ext;

    /**
     * Constructor
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $info = pathinfo($this->path);
        $this->ext = $info['extension'];
    }

    /**
     * Attempts To Compress A JPG Or PNG
     * @throws RuntimeException
     */
    public function compress()
    {
        $command = '';
        $return = '';
        $error = '';

        if (in_array($this->ext, self::COMPRESSABLE)) {
            if (in_array($this->ext, self::JPEG_EXTS)) {
                // Use ImageMagick To Compress Image and Convert To Progressive
                $command = "convert -strip -interlace line " . escapeshellarg($this->path) . " " . escapeshellarg($this->path);
            } else if ($this->ext === 'png') {
                // Use OptiPNG To Compress Image
                $command = "optipng -strip all -backup " . escapeshellarg($this->path) . " -clobber " . escapeshellarg($this->path);
            }

            exec($command, $return, $error);
            if (!empty($error)) {
                throw new RuntimeException('Unable to compress image: ' . $error);
            }
        }
    }
}
