<?php

namespace REW\Backend\Exceptions;

use Exception;

class UnauthorizedPageException extends Exception
{

    protected $message = 'Unauthorized to view page.';
}
