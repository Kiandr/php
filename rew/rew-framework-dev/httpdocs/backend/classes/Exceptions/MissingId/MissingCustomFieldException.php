<?php

namespace REW\Backend\Exceptions\MissingId;

use REW\Backend\Exceptions\MissingIdException;

class MissingCustomFieldException extends MissingIdException
{

    protected $title = 'Missing Custom Field';

    protected $message = 'A custom field with the requested ID does not exist.';
}
