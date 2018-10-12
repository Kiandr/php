<?php

namespace REW\Backend\Leads\CustomField;

use REW\Backend\Leads\CustomField;
use \InvalidArgumentException;
use \Format;

/**
 * Class CustomNumber
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CustomNumber extends CustomField
{

    /**
     * @var string
     */
    const TABLE_FIELD_NUMBER = 'users_field_numbers';

    /**
     * Get Custom Date Type
     * @return string
     */
    public function getType()
    {
        return 'number';
    }

    /**
     * Validate Provided Value
     * @param string $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateValue($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException($this->getName() . ' must be a number.');
        }
        return true;
    }

    /**
     * Parse Provided Value with integer rounding
     * @param mixed $value
     * @return number
     * @throws InvalidArgumentException
     */
    public function parseValue($value)
    {
        return intval(round(floatval($value)));
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE_FIELD_NUMBER;
    }

    /**
     * Get Custom Field Search Join Query
     * @param array $data
     * @return string
     */
    public function getSearchWhere(array $data)
    {

        // Get Value
        $value = !empty($data[$this->getName()]) ? $data[$this->getName()] : null;
        $field = 'ufs_' . $this->getName();

        if (!empty($value)) {
            $whereQuery = "`%s`.`value` = %s";
            return sprintf($whereQuery, $field, $this->db->quote($value));
        }
    }
}
