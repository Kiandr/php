<?php

namespace REW\Api\Exception\Server;

use REW\Api\Exception\ApiExceptionGroup;

class InternalErrorException extends ApiExceptionGroup {

    /**
     * @var string
     */
    const FLD_FILE = 'file';

    /**
     * @var string
     */
    const FLD_LINE = 'line';

    /**
     * @var int
     */
    protected $statusCode = 500;

    /**
     * @var string
     */
    protected $errorType = 'internal_error';

    /**
     * @var string
     */
    protected $message = 'The request failed due to an internal error.';

    /**
     * @var bool
     */
    protected $showDebug = false;

    /**
     * @param bool $showDebug
     */
    public function showDebug ($showDebug) {
        $this->showDebug = !empty($showDebug);
    }

    /**
     * Return unexpected errors in response
     * @return array
     */
    public function getErrorsArray () {
        $errors = [];
        foreach ($this->getErrors() as $prop => $error) {
            if ($error = $this->getErrorData($error)) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    /**
     * Get error data from exception
     * @param \Exception $error
     * @return array
     */
    private function getErrorData ($error) {
        if (!$error) return NULL;
        $errorData = [
            static::FLD_MESSAGE => $error->getMessage(),
            static::FLD_TYPE => get_class($error),
            static::FLD_CODE => $error->getCode()
        ];
        // Add debug information
        if (!empty($this->showDebug)) {
            $errorData += [
                static::FLD_FILE => $error->getFile(),
                static::FLD_LINE => $error->getLine()
            ];
        }
        return array_filter($errorData);
    }

}
