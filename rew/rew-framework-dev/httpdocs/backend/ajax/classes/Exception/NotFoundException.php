<?php

namespace REW\Api\Internal\Exception;

use REW\Api\Internal\Exception\ServerErrorException;

class NotFoundException extends ServerErrorException
{
    /**
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct($message);
        $this->httpCode = 404;
        $this->type = 'missing_resource';
    }
}