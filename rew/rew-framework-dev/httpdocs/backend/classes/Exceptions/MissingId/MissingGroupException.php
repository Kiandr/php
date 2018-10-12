<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingGroupException extends MissingIdException
{

    protected $title = 'Missing Group';
    
    protected $message = 'A group with the requested ID does not exist.';
}
