<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingCategoryException extends MissingIdException
{

    protected $title = 'Missing Blog Category';
    
    protected $message = 'A blog category with the requested ID does not exist.';
}
