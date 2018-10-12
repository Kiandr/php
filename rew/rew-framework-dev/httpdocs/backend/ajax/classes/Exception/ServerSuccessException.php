<?php

namespace REW\Api\Internal\Exception;

use \Exception;

class ServerSuccessException extends Exception
{
    /**
     * @var int
     */
    protected $code;

    /**
     * @var int
     */
    protected $httpCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $message
     */
    public function __construct($message = "")
    {
        $this->code = null;
        $this->httpCode = 200;
        $this->message = $message;
        $this->type = 'successful_request';
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}