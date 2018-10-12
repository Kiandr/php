<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingLinkException extends MissingIdException
{

    protected $title = 'Missing Link';

    protected $message = 'A link with the requested ID does not exist.';
}
