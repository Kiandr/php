<?php

namespace REW\Api\Exception;
use Exception;

abstract class ApiExceptionGroup extends ApiException {

    /**
     * List of exceptions
     * @var Exception[]
     */
    protected $errors = [];

    /**
     * Return exception list
     * @return Exception[]
     */
    public function getErrors () {
        return $this->errors;
    }

    /**
     * Set exception list
     * @param array $errors
     * @return $this
     */
    public function setErrors (array $errors) {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Include grouped errors
     * @return array
     */
    public function toArray () {
        return array_merge(parent::toArray(), [
            'errors' => $this->getErrorsArray()
        ]);
    }

    /**
     * Return grouped errors response as array
     * @return array
     */
    abstract public function getErrorsArray ();

}
