<?php

namespace REW\Backend\Exceptions\MissingId\Directory;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingSnippetException extends MissingIdException
{

    protected $title = 'Missing Directory Snippet';
    
    protected $message = 'A directory snippet with the requested ID does not exist.';
}
