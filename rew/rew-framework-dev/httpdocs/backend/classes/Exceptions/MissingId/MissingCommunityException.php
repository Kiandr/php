<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingCommunityException extends MissingIdException
{

    protected $title = 'Missing Community';
    
    protected $message = 'A community with the requested ID does not exist.';
}
