<?php

namespace REW\Backend\Exceptions;

use Exception;

class SystemErrorException extends Exception
{

    protected $message = 'An error occurred loading the required data.';
}
