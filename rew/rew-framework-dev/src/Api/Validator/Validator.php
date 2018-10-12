<?php

namespace REW\Api\Validator;

use REW\Api\Exception\Request\ValidationFailedException;
use REW\Api\Exception\Validation\InvalidValueException\EmptyRequiredFieldException;

/**
 * @todo: look into replacing this with the in-house validator package.
 */
abstract class Validator
{
    /**
     * Input parameters
     * @var array
     */
    protected $params;

    /**
     * Errors that have been encountered during validation.
     * @var array
     */
    protected $errors = [];

    /**
     * Ensure required fields are present
     * @param array $requiredFields
     * @throws \Exception
     */
    protected function validateRequired($requiredFields = [])
    {
        $this->validateRequiredWithParams($requiredFields, $this->params);
    }

    /**
     * Ensure not empty fields have a value
     * @param array $notEmptyFields
     */
    protected function validateNotEmpty($notEmptyFields = [])
    {
        if (empty($notEmptyFields)) return;
        $params = $this->params;

        foreach ($notEmptyFields as $field) {
            if (isset($params[$field]) && empty($params[$field])) {
                $this->errors[$field] = new EmptyRequiredFieldException($field);
            }
        }
    }

    /**
     * Ensure required fields in provided parameters are present
     * @param array $requiredFields
     * @param array $params
     */
    protected function validateRequiredWithParams($requiredFields = [], $params = [])
    {
        if (empty($requiredFields)) return;

        foreach ($requiredFields as $field) {
            /**
             * Cast parameter value to a string and check its length.
             * This is "smarter" than just performing an empty() check on it because it won't
             * trigger false-positives if the input string is "0".
             */
            $fieldValueString = (string)$params[$field];
            if (strlen($fieldValueString) == 0) {
                $this->errors[$field] = new EmptyRequiredFieldException($field);
            }
        }
    }

    /**
     * Run the validate method for whatever we're dealing with and throw a ValidationFailedException if there are
     * errors at the end.
     * @param array $params
     * @throws \REW\Api\Exception\Request\ValidationFailedException
     */
    public function validateFields (array $params)
    {
        $this->validate($params);

        if (!empty($this->errors)) {
            $validationException = new ValidationFailedException();
            $validationException->setErrors($this->errors);
            throw $validationException;
        }
    }

    /**
     * Validate the input parameters and throw an exception on error
     * @param $params
     */
    protected abstract function validate(array $params);
}
