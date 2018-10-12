<?php

namespace REW\Backend\Exceptions\MissingSettings;

use REW\Backend\Exceptions\MissingIdException;

class MissingIdxSearchException extends MissingIdException
{

    protected $title = 'Missing Idx Search Defaults';
    
    protected $message = 'IDX Search Defaults could not be loaded.';
}
