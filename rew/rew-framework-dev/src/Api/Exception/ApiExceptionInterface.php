<?php

namespace REW\Api\Exception;
use JsonSerializable;

interface ApiExceptionInterface extends JsonSerializable {

    /**
     * HTTP status code
     * @return int
     */
    public function getStatusCode ();

    /**
     * API error type
     * @return string
     */
    public function getErrorType ();

    /**
     * Error message
     * @return string
     */
    public function getMessage ();

    /**
     * API error code
     * @return int
     */
    public function getCode ();

    /**
     * Display as string
     * @return string
     */
    public function __toString ();

    /**
     * Return as array
     * @return array
     */
    public function toArray ();

    /**
     * Serialize as JSON
     * @return mixed
     */
    public function jsonSerialize ();

}
