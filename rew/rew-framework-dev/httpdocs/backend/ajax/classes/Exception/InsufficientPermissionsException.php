<?php

namespace REW\Api\Internal\Exception;

use REW\Api\Internal\Exception\ServerErrorException;

class InsufficientPermissionsException extends ServerErrorException
{
    /**
     * @param string $message
     */
    public function __construct($message = "")
    {
        parent::__construct($message);
        $this->httpCode = 401;
        $this->type = 'unauthorized_request';
    }
}