<?php

namespace REW\Backend\Exceptions;

use Exception;

class UserErrorException extends Exception
{

    protected $message = 'An error occurred processing the provided data.';
}
