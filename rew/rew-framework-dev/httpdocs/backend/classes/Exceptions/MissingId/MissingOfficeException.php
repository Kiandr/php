<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingOfficeException extends MissingIdException
{

    protected $title = 'Missing Office';

    protected $message = 'An office with the requested ID does not exist.';
}
