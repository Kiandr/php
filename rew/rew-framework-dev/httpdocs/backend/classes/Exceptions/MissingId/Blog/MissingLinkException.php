<?php

namespace REW\Backend\Exceptions\MissingId\Blog;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingLinkException extends MissingIdException
{

    /**
     * MissingId Title
     *
     * @var string
     */
    protected $title = 'Missing Blog Link';

    /**
     * MissingId Exception Message
     *
     * @var string
     */
    protected $message = 'A blog link with the requested ID does not exist.';
}
