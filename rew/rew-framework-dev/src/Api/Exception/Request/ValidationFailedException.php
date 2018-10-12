<?php
namespace REW\Api\Exception\Request;

use REW\Api\Exception\ApiExceptionGroup;
use REW\Api\Exception\ValidationException;

class ValidationFailedException extends ApiExceptionGroup {

    /**
     * HTTP status code
     * @var int
     */
    protected $statusCode = 422;

    /**
     * API error type
     * @var string
     */
    protected $errorType = 'validation_error';

    /**
     * API error message
     * @var string
     */
    protected $message = 'The request failed to validate.';

    /**
     * Return failed field rules
     * @return array
     */
    public function getErrorsArray () {
        $errors = [];
        foreach ($this->getErrors() as $prop => $error) {
            $errors[] = ['field' => $prop] + $this->getErrorArray($error);
        }
        return $errors;
    }

    /**
     * Return error containing exception info
     * @param ValidationException $e
     * @return array
     */
    protected function getErrorArray (ValidationException $e) {
        // Standard error info
        return array_filter([
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        ]);
    }
}
