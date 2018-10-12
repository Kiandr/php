<?php

namespace REW\Backend\Leads\Interfaces;

interface CustomFieldInterface
{

    /**
     * @var string
     */
    const CUSTOM_FIELD_FLAG = 'cst_fld_';


    /**
     * Get Custom Field Id
     * @return int
     */
    public function getId();

    /**
     * Get Custom Field Name
     * @return string
     */
    public function getName();

    /**
     * Get Custom Field Title
     * @return string
     */
    public function getTitle();

    /**
     * Get Custom Field Type
     * @return string
     */
    public function getType();

    /**
     * Load Custom Value
     * @param int $lead
     */
    public function loadValue($lead);

    /**
     * Save Value
     * @param int $lead
     * @param mixed $value
     * @throws PDOException
     */
    public function saveValue($lead, $value);

    /**
     * Validate Provided Value
     * @param string $value
     * @return bool
     * @throws InvalidArgumentException
     */
    public function validateValue($value);

    /**
     * Parse Provided Value
     * @param string $value
     * @return string
     */
    public function parseValue($value);

    /**
     * Render Input
     * @param string $value
     * @return string
     */
    public function renderInput($value);
}
