<?php

namespace REW\Api\Exception\Request;

use REW\Api\Exception\ApiException;

class BadRequestException extends ApiException {

    /**
     * @var int
     */
    protected $statusCode = 400;

    /**
     * @var string
     */
    protected $errorType = 'invalid_request';

    /**
     * @var string
     */
    protected $message = 'The API request is invalid or improperly formed.';

}
