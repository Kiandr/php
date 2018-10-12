<?php

namespace REW\Backend\Exceptions;

use InvalidActionException;

class ReportedEmailException extends InvalidActionException
{

    protected $message = 'This recipient has reported an email as SPAM. You cannot send this recipient an email.';
}
