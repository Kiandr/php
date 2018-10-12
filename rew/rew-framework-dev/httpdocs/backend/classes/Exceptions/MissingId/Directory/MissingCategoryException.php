<?php

namespace REW\Backend\Exceptions\MissingId\Directory;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingCategoryException extends MissingIdException
{

    protected $title = 'Missing Directory Category';
    
    protected $message = 'A directory category with the requested ID does not exist.';
}
