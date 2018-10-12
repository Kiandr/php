<?php

namespace REW\Backend\Leads\CustomField;

use REW\Backend\Leads\CustomField;
use \InvalidArgumentException;

/**
 * Class CustomDate
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class CustomDate extends CustomField
{

    /**
     * @var string
     */
    const TABLE_FIELD_DATE = 'users_field_dates';

    /**
     * Get Custom Date Type
     * @return string
     */
    public function getType()
    {
        return 'date';
    }

    /**
     * Validate Provided Value
     * @param string $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateValue($value)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date) {
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }
        if (!$date) {
            throw new InvalidArgumentException($this->getTitle() . ' must be a date.');
        }
        return true;
    }

    /**
     * Parse Provided Value
     * @param string $value
     * @return string
     */
    public function parseValue($value)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$date) {
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }
        return date('Y-m-d H:i:s', $date->getTimestamp());
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE_FIELD_DATE;
    }

    /**
     * Get Custom Field Search Join Query
     * @param array $data
     * @return string
     */
    public function getSearchWhere(array $data)
    {

        // Get Dates
        $start= !empty($data[$this->getName() . '_start']) ? $data[$this->getName() . '_start'] . ' 00:00:00': null;
        $end= !empty($data[$this->getName() . '_end']) ? $data[$this->getName() . '_end']. ' 23:59:25': null;
        $field = 'ufs_' . $this->getName();

        if (!empty($start) || !empty($end)) {
            if (!empty($start) && !empty($end)) {
                return sprintf("`%s`.`value` >= %s AND `%s`.`value` <= %s", $field, $this->db->quote($start), $field, $this->db->quote($end));
            } elseif (!empty($start)) {
                return sprintf("`%s`.`value` >= %s", $field, $this->db->quote($start));
            } elseif (!empty($end)) {
                return sprintf("`%s`.`value` <= %s", $field, $this->db->quote($end));
            }
        }
    }

    /**
     * Get Custom Field Search Where Query
     * @param array $data
     * @param string $alias
     * @return string
     */
    public function getSearchJoin(array $data, $alias)
    {

        // Get Value
        $start= !empty($data[$this->getName() . '_start']) ? $data[$this->getName() . '_start'] . ' 00:00:00': null;
        $end= !empty($data[$this->getName() . '_end']) ? $data[$this->getName() . '_end']. ' 23:59:25': null;
        $field = 'ufs_' . $this->getName();

        if (!empty($start) || !empty($end)) {
            $joinQuery = " LEFT JOIN `%s` `%s` ON (`%s`.`id` = `%s`.`user_id` AND `%s`.`field_id` = %s)";
            return sprintf($joinQuery, $this->getTable(), $field, $alias, $field, $field, $this->getId());
        }
    }

    /**
     * Get Custom Field Search Criteria String
     * @param array $data
     * @return string
     */
    public function getSearchString(array $data)
    {

        // Get Dates
        $start= !empty($data[$this->getName() . '_start']) ? $data[$this->getName() . '_start'] : null;
        $end= !empty($data[$this->getName() . '_end']) ? $data[$this->getName() . '_end'] : null;

        if (!empty($start) || !empty($end)) {
            if (!empty($start) && !empty($end)) {
                return sprintf('<strong>%s Between:</strong> %s and %s', $this->format->htmlspecialchars($this->getTitle()), date('F j, Y', strtotime($start)), date('F j, Y', strtotime($end)));
            } elseif (!empty($start)) {
                return sprintf('<strong>%s After:</strong> %s', $this->format->htmlspecialchars($this->getTitle()), date('F j, Y', strtotime($start)));
            } elseif (!empty($end)) {
                return sprintf('<strong>%s Before:</strong> %s', $this->format->htmlspecialchars($this->getTitle()), date('F j, Y', strtotime($end)));
            }
        }
    }
}
