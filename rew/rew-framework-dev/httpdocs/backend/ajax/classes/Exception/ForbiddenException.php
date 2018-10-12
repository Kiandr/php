<?php

namespace REW\Api\Internal\Exception;

use REW\Api\Internal\Exception\ServerErrorException;

class ForbiddenException extends ServerErrorException
{
    /**
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct($message);
        $this->httpCode = 403;
        $this->type = 'forbidden_request';
    }
}