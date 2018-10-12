<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingSearchException extends MissingIdException
{

    protected $title = 'Missing Search';

    protected $message = 'A search with the requested ID does not exist.';
}
