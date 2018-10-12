<?php

namespace REW\Backend\Exceptions\MissingSettings;

use REW\Backend\Exceptions\MissingIdException;

class MissingDirectoryException extends MissingIdException
{

    protected $title = 'Missing Directory Settings';
    
    protected $message = 'Directory Settings could not be loaded.';
}
