<?php

namespace REW\Backend\Exceptions\MissingSettings;

use REW\Backend\Exceptions\MissingIdException;

class MissingIdxMetaException extends MissingIdException
{

    protected $title = 'Missing Idx Meta Settings';
    
    protected $message = 'Meta Information Settings could not be loaded.';
}
