<?php

namespace REW\Backend\Leads;

use REW\Backend\Leads\Interfaces\CustomFieldInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Backend\Leads\CustomFieldValue;
use \InvalidArgumentException;
use \PDOException;

/**
 * Class CustomField
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
abstract class CustomField implements CustomFieldInterface
{

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var CustomFieldValue
     */
    protected $value;

    /**
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param int $id
     * @param string $name
     * @param string $title
     * @param enabled $enabled
     * @throws \InvalidArgumentException
     */
    public function __construct(DBInterface $db, FormatInterface $format, $id, $name, $title, $enabled)
    {

        // Set Variables
        $this->db    = $db;
        $this->format = $format;
        $this->id    = $id;
        $this->name  = $name;
        $this->title = $title;
        $this->enabled = $enabled;
    }

    /**
     * Get Custom Field Id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Custom Field Name
     * @return string
     */
    public function getName()
    {
        return self::CUSTOM_FIELD_FLAG . $this->name;
    }

    /**
     * Get Custom Field Title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get Custom Field Type
     * @return string
     */
    abstract public function getType();

    /**
     * Is this field enabled
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled ? true : false;
    }

    /**
     * Load Custom Value
     * @param unknown $lead
     */
    public function loadValue($lead)
    {
        $customFieldsQuery = $this->db->prepare(
            'SELECT `value`'
            . ' FROM `' . $this->getTable() . '`'
            . ' WHERE `user_id` = :user_id'
            . ' AND `field_id` = :field_id'
        );
        $customFieldsQuery->execute([
            'user_id' => $lead,
            'field_id' => $this->id
        ]);
        return $customFieldsQuery->fetchColumn();
    }

    /**
     * Save Value
     * @param int $lead
     * @param mixed $value
     * @returns true
     * @throws PDOException
     */
    public function saveValue($lead, $value)
    {
        try {
            $insertCustom = $this->db->prepare(
                "REPLACE INTO `" . $this->getTable() . "` SET "
                . "`user_id` = :user_id, "
                . "`field_id` = :field_id, "
                . "`value` = :value;"
            );
            $insertCustom->execute([
                'user_id' => $lead,
                'field_id' => $this->id,
                'value' => $value,
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return true;
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
            $whereQuery = "`%s`.`value` LIKE %s";
            return sprintf($whereQuery, $field, $this->db->quote('%'.$value.'%'));
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
        $value = !empty($data[$this->getName()]) ? $data[$this->getName()] : null;
        $field = 'ufs_' . $this->getName();

        if (!empty($value)) {
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
        $value = !empty($data[$this->getName()]) ? $data[$this->getName()] : null;

        if (!empty($value)) {
            return '<strong>' . $this->format->htmlspecialchars($this->getTitle()) . ':</strong> ' . $this->format->htmlspecialchars($value);
        }
    }

    /**
     * Validate Provided Value
     * @param string $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateValue($value)
    {
        return true;
    }

    /**
     * Parse Provided Value
     * @param string $value
     * @return string
     */
    public function parseValue($value)
    {
        return $value;
    }

    /**
     * Render Input
     * @param string $value
     * @return string
     */
    public function renderInput($value)
    {

        // Get Type
        if ($this->getType() == 'number') {
            $type = ' type="number"';
        }
        if ($this->getType() == 'date') {
            $type = ' type="text"';
        }
        if ($this->getType() == 'string') {
            $length = ' maxlength="255"';
        }

        // Get Value
        if (!empty($value)) {
            $value = ' value="' . $this->format->htmlspecialchars($value) . '"';
        }

        $placeholder = $this->getType() == 'date' ? ' placeholder="yyyy-mm-dd"' : '';

        // Render Input
        return '<div class="fld">'
            . '<label class="fld-label">' . $this->format->htmlspecialchars($this->getTitle()) . '</label>'
                . '<input class="w1/1" name="' . $this->format->htmlspecialchars($this->getName()) . '"' . $type . $length . $value . $placeholder . '>'
            .'</div>';
    }

    /**
     * Get Number of Leads using this custom field
     * @param string $value
     * @return string
     */
    public function getUsage()
    {
        $customFieldCountQuery = $this->db->prepare('SELECT `id` FROM `' . $this->getTable(). '` WHERE `field_id` = :field_id');
        $customFieldCountQuery->execute(['field_id' => $this->getId()]);
        return $customFieldCountQuery->rowCount();
    }

    /**
     * Get Table that Values are saved to or loaded from
     * @param unknown $lead
     */
    abstract public function getTable();
}
