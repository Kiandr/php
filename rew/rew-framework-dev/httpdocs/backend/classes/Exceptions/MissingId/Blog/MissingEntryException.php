<?php

namespace REW\Backend\Exceptions\MissingId\Blog;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingEntryException extends MissingIdException
{

    /**
     * MissingId Title
     *
     * @var string
     */
    protected $title = 'Missing Blog Entry';

    /**
     * MissingId Exception Message
     *
     * @var string
     */
    protected $message = 'A blog entry with the requested ID does not exist.';
}
