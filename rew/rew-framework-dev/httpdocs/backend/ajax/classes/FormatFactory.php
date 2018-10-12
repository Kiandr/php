<?php

namespace REW\Api\Internal;

use REW\Api\Internal\Exception\ServerErrorException;
use REW\Api\Internal\Format\Boolean;
use REW\Api\Internal\Format\Standard;
use REW\Api\Internal\Format\Timestamp;

class FormatFactory
{
    /**
     * Create a formatter class
     *
     * @param string $type ('boolean' || 'standard' || 'timestamp')
     * @param array $unformattedData
     * @param array $possibleFields
     * @param array $requestedFields
     *
     * @throws Exception
     *
     * @return object
     */
    public static function create($type, $unformattedData = [], $possibleFields = [], $requestedFields = [])
    {
        $allowed_types = ['boolean', 'standard', 'timestamp'];
        if (!in_array($type, $allowed_types)) {
            throw new ServerErrorException('Failed to format response data - invalid format type requested.');
        }
        switch ($type) {
            case 'boolean':
                return new Boolean($unformattedData, $possibleFields, $requestedFields);
                break;
            case 'standard':
                return new Standard($unformattedData, $possibleFields, $requestedFields);
                break;
            case 'timestamp':
                return new Timestamp($unformattedData, $possibleFields, $requestedFields);
                break;
        }
    }
}