<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingLenderException extends MissingIdException
{

    protected $title = 'Missing Lender';
    
    protected $message = 'An associate with the requested ID does not exist.';
}
