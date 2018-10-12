<?php

/**
 * Backend_Uploader_Xhr is used to handle file uploads via XMLHttpRequest
 * @package Backend_Uploader
 */
class Backend_Uploader_Xhr extends Backend_Uploader
{

    /**
     * @see Backend_Uploader::getName()
     */
    public function getName()
    {
        if (is_null($this->name)) {
            $this->name = $_GET[$this->_file];
        }
        return $this->name;
    }

    /**
     * @throws Exception If Content-Length is not available
     * @see Backend_Uploader::getSize()
     */
    public function getSize()
    {
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            return (int) $_SERVER['CONTENT_LENGTH'];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

    /**
     * @see Backend_Uploader::getType()
     */
    public function getType()
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($this->read());
    }

    /**
     * @see Backend_Uploader::read()
     */
    public function read()
    {
        $input = fopen('php://input', 'r');
        $data = stream_get_contents($input);
        fclose($input);
        if (strlen($data) != $this->getSize()) {
            return new Exception('The file you are trying upload was only partially uploaded.');
        }
        return $data;
    }

    /**
     * @see Backend_Uploader::save()
     */
    public function save($file)
    {
        $input = fopen('php://input', 'r');
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        if ($realSize != $this->getSize()) {
            return false;
        }
        $target = fopen($file, 'w');
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        return true;
    }
}
