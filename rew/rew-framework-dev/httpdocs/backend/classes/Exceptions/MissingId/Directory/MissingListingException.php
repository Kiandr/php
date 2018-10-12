<?php

namespace REW\Backend\Exceptions\MissingId\Directory;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingListingException extends MissingIdException
{

    protected $title = 'Missing Directory Listing';
    
    protected $message = 'A directory listing with the requested ID does not exist.';
}
