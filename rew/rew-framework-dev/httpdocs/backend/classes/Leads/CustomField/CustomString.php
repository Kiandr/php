<?php

namespace REW\Backend\Leads\CustomField;

use REW\Backend\Leads\CustomField;

/**
 * Class CustomString
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CustomString extends CustomField
{

    /**
     * @var string
     */
    const TABLE_FIELD_STRING = 'users_field_strings';

    /**
     * Get Custom Date Type
     * @return string
     */
    public function getType()
    {
        return 'text';
    }

    /**
     * Validate Provided Value
     * @param string $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateValue($value)
    {
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException($this->getName() . ' must be shorter then 256 characters.');
        }
        return true;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE_FIELD_STRING;
    }
}
