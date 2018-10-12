<?php

namespace REW\Backend\Exceptions;

use REW\Backend\Exceptions\SystemErrorException;

class MissingSettingsException extends SystemErrorException
{

    protected $title = 'Missing Settings';

    protected $message = 'The required settings could not be loaded.';

    public function getTitle()
    {
        return $this->title;
    }
}
