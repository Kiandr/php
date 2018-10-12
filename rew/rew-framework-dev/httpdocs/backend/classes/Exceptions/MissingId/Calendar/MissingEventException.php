<?php

namespace REW\Backend\Exceptions\MissingId\Calendar;

use REW\Backend\Exceptions\MissingIdException;

class MissingEventException extends MissingIdException
{

    protected $title = 'Missing Calendar Event';

    protected $message = 'A calendar event with the requested ID does not exist.';
}
