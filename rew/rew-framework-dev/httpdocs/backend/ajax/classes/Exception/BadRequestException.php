<?php

namespace REW\Api\Internal\Exception;

use REW\Api\Internal\Exception\ServerErrorException;

class BadRequestException extends ServerErrorException
{
    /**
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct($message);
        $this->httpCode = 400;
        $this->type = 'invalid_request';
    }
}