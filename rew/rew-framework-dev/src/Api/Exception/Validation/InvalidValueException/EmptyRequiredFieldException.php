<?php
namespace REW\Api\Exception\Validation\InvalidValueException;

use REW\Api\Exception\Validation\InvalidValueException;

class EmptyRequiredFieldException extends InvalidValueException
{
    public function __construct($field)
    {
        parent::__construct($field, null);
        $this->message = sprintf('Required field "%s" is unexpectedly empty.', $this->field);
    }
}
