<?php

namespace REW\Api\Internal\Format;

use REW\Api\Internal\Interfaces\FormatInterface;

class Standard implements FormatInterface
{
    /**
     * Raw API response data
     *
     * @var array
     */
    private $unformattedData;

    /**
     * All possible fields for API response
     *
     * @var array
     */
    private $possibleFields;

    /**
     * Fields requested by API
     *
     * @var unknown_type
     */
    private $requestedFields;

    public function __construct($unformattedData = [], $possibleFields = [], $requestedFields = [])
    {
        $this->unformattedData = $unformattedData;
        $this->possibleFields = $possibleFields;
        $this->requestedFields = $requestedFields;
    }

    /**
     * Format Standard (String|Array|Object) Response Fields
     *
     * @param array $unformattedData
     * @param array $possibleFields
     * @param array $requestedFields
     * @return array
     */
    public function format()
    {
        $return = [];
        foreach ($this->possibleFields as $field) {
            if (!empty($this->requestedFields) && !in_array($field, $this->requestedFields)) {
                continue;
            }
            if (!empty($this->unformattedData[$field])) {
                if (is_array($dataSet[$field]) || is_object($this->unformattedData[$field])) {
                    $return[$field] = json_encode($this->unformattedData[$field]);
                } else {
                    $return[$field] = $this->unformattedData[$field];
                }
            } else {
                $return[$field] = null;
            }
        }
        return $return;
    }
}