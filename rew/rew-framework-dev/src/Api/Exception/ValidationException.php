<?php
namespace REW\Api\Exception;

abstract class ValidationException extends \Exception
{
    const FLD_FIELD = 'field';

    const FLD_VALUE = 'value';

    protected $message;

    /**
     * The name of the field that failed validation.
     * @var string
     */
    protected $field;

    /**
     * The value of the field that failed validation.
     * @var mixed
     */
    protected $value;

    /**
     * @param string $field
     * @param mixed $value
     */
    public function __construct($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->message = sprintf('Validation failed for field "%s" with value "%s"', $this->field, $this->value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            static::FLD_FIELD => $this->field,
            static::FLD_VALUE => $this->value
        ];
    }
}
