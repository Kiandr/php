<?php
namespace REW\Api\Exception\Validation;

use REW\Api\Exception\ValidationException;

class InvalidTypeException extends ValidationException
{
    const FLD_EXPECTED_TYPE = 'expected_type';

    const FLD_ACTUAL_TYPE = 'actual_type';

    /**
     * The expected type of this field.
     * @var string
     */
    protected $expectedType;

    /**
     * The actual type introduced into this field.
     * @var string
     */
    protected $actualType;

    /**
     * @param string $field
     * @param mixed $value
     * @param string $expectedType
     */
    public function __construct($field, $value, $expectedType)
    {
        parent::__construct($field, $value);
        $this->actualType = gettype($value);
        $this->expectedType = $expectedType;
        $this->message = sprintf('"%s" is not of required type "%s".', $this->value, $this->expectedType);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $parentJson = parent::toArray();
        $parentJson[static::FLD_EXPECTED_TYPE] = $this->expectedType;
        $parentJson[static::FLD_ACTUAL_TYPE] = $this->actualType;
        return $parentJson;
    }
}
