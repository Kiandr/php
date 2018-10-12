<?php

namespace REW\Backend\Exceptions\MissingId;

use Exception;
use REW\Backend\Exceptions\MissingIdException;

class MissingCampaignException extends MissingIdException
{

    protected $title = 'Missing Campaign';
    
    protected $message = 'A campaign with the requested ID does not exist.';
}
