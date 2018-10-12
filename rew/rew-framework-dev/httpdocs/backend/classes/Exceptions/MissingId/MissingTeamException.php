<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingTeamException extends MissingIdException
{

    protected $title = 'Missing Team';
    
    protected $message = 'A team with the requested ID does not exist.';
}
