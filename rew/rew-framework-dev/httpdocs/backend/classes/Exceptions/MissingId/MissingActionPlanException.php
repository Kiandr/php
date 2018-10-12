<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingActionPlanException extends MissingIdException
{

    protected $title = 'Missing Action Plan';
    
    protected $message = 'An action plan with the requested ID does not exist.';
}
