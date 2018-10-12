<?php

/**
 * Search by City
 * @package IDX_Panel
 */
class IDX_Panel_City extends IDX_Panel_Type_Dynamic
{

    /**
     * Panel Title
     * @var string
     */
    public $title = 'City';

    /**
     * Input Name
     * @var string
     */
    public $inputName = 'search_city';

    /**
     * Class Name for Input Field
     * @var string
     */
    protected $inputClass = 'location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'AddressCity';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Checklist';

    /**
     * Panel Class
     * @var string
     */
    protected $panelClass = 'scrollable';

    /**
     * @see IDX_Panel_Type_Dynamic::__construct()
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        if ($this->fieldType !== 'Checklist') {
            $this->inputClass .= ' x12';
        }
    }

    /**
     * Get City List
     * @return array
     */
    public function getOptions()
    {
        // Client List
        global $_CLIENT;
        if (!empty($_CLIENT['city_list'])) {
            $options = $_CLIENT['city_list'];
        // Load Cities
        } else {
            $options = parent::getOptions();
        }
        // Prepend if needed
        if (!empty($options) && is_array($options)) {
            if ($this->fieldType === 'Select') {
                $options = array_merge(array(
                    array('value' => '', 'title' => 'Select a City')
                ), $options);
            }
        }
        // Return List
        return $options;
    }

    public function typeSpecificJsonSerialize()
    {
        return array_merge(parent::typeSpecificJsonSerialize(),
            [
                'options' => $this->getOptions()
            ]);
    }
}
