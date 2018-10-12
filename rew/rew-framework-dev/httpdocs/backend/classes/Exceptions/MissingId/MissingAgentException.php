<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingAgentException extends MissingIdException
{

    protected $title = 'Missing Agent';
    
    protected $message = 'An agent with the requested ID does not exist.';
}
