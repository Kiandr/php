<?php
namespace REW\Api\Exception\Validation;

use REW\Api\Exception\ValidationException;

class InvalidValueException extends ValidationException
{
    /**
     * @param string $field
     * @param mixed $value
     * @param string $message
     */
    public function __construct($field, $value, $message = '')
    {
        parent::__construct($field, $value);
        if ($message !== '') {
            $this->message = $message;
        }
    }
}
