<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingPageException extends MissingIdException
{

    protected $title = 'Missing Page';

    protected $message = 'A page with the requested ID does not exist.';
}
