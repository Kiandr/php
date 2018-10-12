<?php
namespace REW\Api\Exception\Validation\InvalidValueException;

use REW\Api\Exception\Validation\InvalidValueException;

class InvalidJsonException extends InvalidValueException
{
    public function __construct($field, $value, $jsonError)
    {
        parent::__construct($field, $value);
        $this->message = sprintf('Field "%s" does not contain valid JSON: %s', $this->field, $jsonError);
    }
}
