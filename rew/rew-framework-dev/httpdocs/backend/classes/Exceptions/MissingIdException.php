<?php

namespace REW\Backend\Exceptions;

use REW\Backend\Exceptions\UserErrorException;

class MissingIdException extends UserErrorException
{

    protected $title = 'Missing ID';

    protected $message = 'The requested id does not exist.';

    public function getTitle()
    {
        return $this->title;
    }
}
