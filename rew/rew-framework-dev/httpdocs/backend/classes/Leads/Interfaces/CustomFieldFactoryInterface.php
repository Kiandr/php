<?php

namespace REW\Backend\Leads\Interfaces;

interface CustomFieldFactoryInterface
{

    /**
     * User Fields Table
     * @var string
     */
    const FIELDS_TABLE = 'users_fields';

    /**
     * Load a custom fields
     * @param int $id
     * @return CustomFieldInterface
     */
    public function loadCustomField($id);

    /**
     * Load an array of custom fields
     * @return CustomFieldInterface[]
     */
    public function loadCustomFields();

    /**
     * Get Custom Fields Table
     * @return string
     */
    public function getTable();

    /**
     * Get Possible Custom Field Types
     * @return array
     */
    public function getTypes();
}
