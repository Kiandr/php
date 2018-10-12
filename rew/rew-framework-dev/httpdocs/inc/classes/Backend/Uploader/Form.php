<?php

/**
 * Backend_Uploader_Form is used to handle file uploads via regular form post (uses the $_FILES array)
 * @package Backend_Uploader
 */
class Backend_Uploader_Form extends Backend_Uploader
{

    /**
     * @see Backend_Uploader::getName()
     */
    public function getName()
    {
        if (is_null($this->name)) {
            $this->name = $_FILES[$this->_file]['name'];
        }
        return $this->name;
    }

    /**
     * @see Backend_Uploader::getSize()
     */
    public function getSize()
    {
        return $_FILES[$this->_file]['size'];
    }

    /**
     * @see Backend_Uploader::getType()
     */
    public function getType()
    {
        return $_FILES[$this->_file]['type'];
    }

    /**
     * @see Backend_Uploader::read()
     */
    public function read()
    {
        $file = $_FILES[$this->_file]['tmp_name'];
        if (!is_uploaded_file($file)) {
            throw new Exception($this->_error());
        }
        return file_get_contents($file);
    }

    /**
     * @see Backend_Uploader::save()
     */
    public function save($file)
    {
        $source = $_FILES[$this->_file]['tmp_name'];
        if (!move_uploaded_file($source, $file)) {
            throw new Exception($this->_error());
        }
        return true;
    }

    /**
     * Return Error Message
     * @return string
     */
    private function _error()
    {
        switch ($_FILES[$this->file]['error']) {
            case 1: // uploaded file exceeds the upload_max_filesize directive in php.ini
                return 'The file you are trying to upload is too big.';
                break;
            case 2: // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                return 'The file you are trying to upload is too big.';
                break;
            case 3: // uploaded file was only partially uploaded
                return 'The file you are trying upload was only partially uploaded.';
                break;
            case 4: // no file was uploaded
                return 'You must select an image for upload.';
                break;
            case 0: // no error; possible file attack!
            default: // a default error, just in case!  :)
                return 'There was a problem with your upload.';
                break;
        }
    }
}
