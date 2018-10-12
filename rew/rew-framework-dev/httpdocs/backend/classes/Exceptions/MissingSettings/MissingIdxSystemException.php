<?php

namespace REW\Backend\Exceptions\MissingSettings;

use REW\Backend\Exceptions\MissingIdException;

class MissingIdxSystemException extends MissingIdException
{

    protected $title = 'Missing Idx System Settings';
    
    protected $message = 'IDX System Settings could not be loaded.';
}
