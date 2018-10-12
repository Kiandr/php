<?php

namespace REW\Backend\Exceptions\MissingId;

use REW\Backend\Exceptions\MissingIdException;

class MissingLeadException extends MissingIdException
{

    protected $title = 'Missing Lead';

    protected $message = 'A lead with the requested ID does not exist.';
}
