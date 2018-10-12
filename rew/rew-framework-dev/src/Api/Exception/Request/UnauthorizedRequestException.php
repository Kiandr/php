<?php
namespace REW\Api\Exception\Request;
use REW\Api\Exception\ApiException;

class UnauthorizedRequestException extends ApiException {

    /**
     * @var int
     */
    protected $statusCode = 401;

    /**
     * @var string
     */
    protected $errorType = 'unauthorized';

    /**
     * @var string
     */
    protected $message = 'Authentication is required.';

}
