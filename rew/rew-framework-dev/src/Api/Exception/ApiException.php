<?php
namespace REW\Api\Exception;
use \Exception;

abstract class ApiException extends Exception implements ApiExceptionInterface {

    /**
     * @var string
     */
    const FLD_MESSAGE = 'message';

    /**
     * @var string
     */
    const FLD_TYPE = 'type';

    /**
     * @var string
     */
    const FLD_CODE = 'code';

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $errorType;

    /**
     * @var \Exception
     */
    protected $previous;

    /**
     * ApiException constructor.
     * @param null $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct ($message = NULL, $code = 0, Exception $previous = NULL) {
        $this->previous = $previous;
        if (!is_null($message)) {
            $this->message = $message;
        }
        if (!empty($code)) {
            $this->code = $code;
        }
    }

    /**
     * @return int
     */
    public function getStatusCode () {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getErrorType () {
        return $this->errorType;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize () {
        return array_filter($this->toArray());
    }

    /**
     * @return array
     */
    public function toArray () {
        return [
            static::FLD_MESSAGE => $this->getMessage(),
            static::FLD_TYPE => $this->getErrorType(),
            static::FLD_CODE => $this->getCode()
        ];
    }

}
