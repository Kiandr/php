<?php

// @todo: create dynamic panel instance in __construct

/**
 * Dynamic Field Type
 * @package IDX_Panel
 */
abstract class IDX_Panel_Type_Dynamic extends IDX_Panel implements IDX_Panel_Interface_Taggable
{
    const PANEL_TYPE = 'dynamic';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType;

    /**
     * Field Options
     * @var array
     */
    protected $fieldOptions = array();

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['fieldType'])) {
            $this->fieldType = $options['fieldType'];
        }
        parent::__construct($options);
    }

    /**
     * Panel Type
     * @param string $fieldType
     * @return void
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * Panel Options
     * @param array $options
     */
    public function setFieldOptions($options = array())
    {
        $this->fieldOptions = $options;
    }

    /**
     * Return Field Panel Type
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Return Field Panel Options
     * @return array $options
     */
    public function getFieldOptions()
    {
        return $this->fieldOptions;
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        return array_map(function ($value) {
            return new IDX_Search_Tag($value, array($this->inputName => $value));
        }, $this->getValues());
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {

        // Create Dynamic Panel
        $fieldType = 'IDX_Panel_Type_' . $this->fieldType;
        if (class_exists($fieldType)) {
            // Load Options
            $options = null;
            if (in_array($this->fieldType, array('Checklist', 'Radiolist', 'Select'))) {
                $options = $this->getOptions();
            }

            // Create new panel instance - use this panel's properties and options
            $field = new $fieldType(array_merge(get_object_vars($this), array(
                'options' => $options
            ), $this->fieldOptions));

            // Return Field HTML
            return $field->getMarkup();
        }

        // Invalid
        return null;
    }
}
