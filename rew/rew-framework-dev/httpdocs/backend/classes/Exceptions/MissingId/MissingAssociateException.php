<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingAssociateException extends MissingIdException
{

    protected $title = 'Missing Associate';
    
    protected $message = 'An associate with the requested ID does not exist.';
}
