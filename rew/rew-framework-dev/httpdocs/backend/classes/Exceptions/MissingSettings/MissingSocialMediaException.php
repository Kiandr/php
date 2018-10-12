<?php

namespace REW\Backend\Exceptions\MissingSettings;

use REW\Backend\Exceptions\MissingIdException;

class MissingSocialMediaException extends MissingIdException
{

    protected $title = 'Missing Social Media Settings';
    
    protected $message = 'Social Media Settings could not be found.';
}
