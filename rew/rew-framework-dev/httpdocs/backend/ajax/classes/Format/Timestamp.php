<?php

namespace REW\Api\Internal\Format;

use REW\Api\Internal\Interfaces\FormatInterface;

class Timestamp implements FormatInterface
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
     * Format Timestamp Response Fields
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
            $return[$field] = !empty($this->unformattedData[$field]) ? $this->unformattedData[$field] : '0000-00-00 00:00:00';
        }
        return $return;
    }
}