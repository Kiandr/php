<?php

namespace REW\Backend\Leads;

use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;
use REW\Backend\Leads\Interfaces\CustomFieldInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\ContainerInterface;

class CustomFieldFactory implements CustomFieldFactoryInterface
{

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var []
     */
    protected $fields;

    /**
     * @param SettingsInterface $settings
     * @param DBInterface $db
     * @param ContainerInterface $container
     */
    public function __construct(SettingsInterface $settings, DBInterface $db, ContainerInterface $container)
    {
        $this->settings = $settings;
        $this->db = $db;
        $this->container= $container;
    }

    /**
     * Get a specific custom fields
     * @param int $id
     * @return CustomFieldInterface|false
     */
    public function loadCustomField($id)
    {

        // Load Custom Field Data
        $customFieldQuery = $this->db->prepare('SELECT `name`, `title`, `type`, `enabled` FROM `' . $this->getTable() . '` WHERE `id` = :id');
        $customFieldQuery->execute(['id' => $id]);
        $customField = $customFieldQuery->fetch();

        $validCustomFieldTypes = $this->getCustomFieldTypes();
        if (empty($validCustomFieldTypes)) {
            return false;
        }
        if (in_array($customField['type'], array_keys($validCustomFieldTypes))) {
            $fieldSettings = $validCustomFieldTypes[$customField['type']];

            $field = $this->container->make(
                $fieldSettings['class'],
                ['id' => $id, 'name' => $customField['name'], 'title' => $customField['title'], 'enabled' => $customField['enabled']]
            );
            if ($field instanceof CustomFieldInterface) {
                return $field;
            }
        }
        return false;
    }

    /**
     * Get an array of custom fields
     * @param bool|NULL Query only enabled
     * @return CustomFieldInterface[]
     */
    public function loadCustomFields($enabled = null)
    {

        // Formatted Fields
        $fields = [];

        $enabledQuery = '';
        if (isset($enabled) && $enabled === true) {
            $enabledQuery= ' WHERE `enabled` = 1';
        }
        if (isset($enabled) && $enabled === false) {
            $enabledQuery= ' WHERE `enabled` = 0';
        }

        // Load Custom Field Data
        $customFieldsQuery = $this->db->prepare('SELECT `id`, `name`, `title`, `type`, `enabled` FROM `' . $this->getTable() . '`' . $enabledQuery. ' ORDER BY `id`');
        $customFieldsQuery->execute();
        $customFields = $customFieldsQuery->fetchAll();

        $validCustomFieldTypes = $this->getCustomFieldTypes();
        if (empty($validCustomFieldTypes)) {
            return [];
        }
        foreach ($customFields as $k => $customField) {
            if (in_array($customField['type'], array_keys($validCustomFieldTypes))) {
                $fieldSettings = $validCustomFieldTypes[$customField['type']];
                $field = $this->container->make(
                    $fieldSettings['class'],
                    ['id' => $customField['id'], 'name' => $customField['name'], 'title' => $customField['title'], 'enabled' => $customField['enabled']]
                );
                if ($field instanceof CustomFieldInterface) {
                    $fields[]= $field;
                }
            }
        }
        return $fields;
    }

    /**
     * Get enabled custom fields
     * @return unknown
     */
    public function loadEnabledCustomFields()
    {
        return $this->loadCustomFields(true);
    }

    /**
     * Get disabled custom fields
     * @return unknown
     */
    public function loadDisabledCustomFields()
    {
        return $this->loadCustomFields(false);
    }

    /**
     * Get Custom Fields Table
     * @return string
     */
    public function getTable()
    {
        return self::FIELDS_TABLE;
    }

    /**
     * Get Possible Custom Field Types
     * @return array
     */
    public function getTypes()
    {
        $types = [];
        $validCustomFieldTypes = $this->getCustomFieldTypes();
        if (empty($validCustomFieldTypes)) {
            return $types;
        }
        foreach ($validCustomFieldTypes as $k => $customField) {
            $types[$k] = $customField['title'];
        }
        return $types;
    }

    /**
     * Get all custom field options
     * @return array
     */
    protected function getCustomFieldTypes()
    {
        return $this->settings['CUSTOM_FIELD_TYPES'];
    }
}
