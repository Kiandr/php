<?php

namespace REW\Backend\Exceptions;

use Exception;

class InvalidActionException extends Exception
{

    protected $message = 'The requested action is invalid.';
}
