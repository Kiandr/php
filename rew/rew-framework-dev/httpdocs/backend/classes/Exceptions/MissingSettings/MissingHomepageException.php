<?php

namespace REW\Backend\Exceptions\MissingSettings;

use REW\Backend\Exceptions\MissingIdException;

class MissingHomepageException extends MissingIdException
{

    protected $title = 'Missing Homepage Settings';
    
    protected $message = 'Subdomain Homepage could not be found.';
}
