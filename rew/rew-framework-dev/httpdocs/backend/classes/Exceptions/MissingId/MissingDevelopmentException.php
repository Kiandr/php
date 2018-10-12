<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingDevelopmentException extends MissingIdException
{

    protected $title = 'Missing Development';
    
    protected $message = 'A development with the requested ID does not exist.';
}
