<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingSnippetException extends MissingIdException
{

    protected $title = 'Missing Snippet';

    protected $message = 'A snippet with the requested ID does not exist.';
}
