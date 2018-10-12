<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingListingException extends MissingIdException
{

    protected $title = 'Missing Listing';

    protected $message = 'A listing with the requested ID does not exist.';
}
