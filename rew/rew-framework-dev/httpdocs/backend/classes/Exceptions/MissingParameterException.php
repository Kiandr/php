<?php

namespace REW\Backend\Exceptions;

use REW\Backend\Exceptions\UserErrorException;

class MissingParameterException extends UserErrorException
{

    protected $title = 'Missing Required Parameter';

    protected $message = 'A required parameter was not provided.';
}
