<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingRecipientException extends MissingIdException
{

    protected $title = 'Missing Recipient';

    protected $message = 'No recipient provided.';
}
