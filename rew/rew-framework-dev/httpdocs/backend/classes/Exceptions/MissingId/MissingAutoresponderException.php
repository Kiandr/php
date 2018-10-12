<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingAutoresponderException extends MissingIdException
{

    protected $title = 'Missing Autoresponder';
    
    protected $message = 'An autoresponder with the requested ID does not exist.';
}
