<?php

/**
 * Search by Location
 * @package IDX_Panel
 */
class IDX_Panel_Location extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Location';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_location';

    /**
     * Class Name for Input Field
     * @var string
     */
    protected $inputClass = 'x12 autocomplete location';

    /**
     * Set Placeholder Text on Construct (Because It's Dynamic)
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        if (empty($this->placeholder) && $options['placeholder'] !== false) {
            $this->placeholder = 'City, ' . Locale::spell('Neighborhood') . ', Address, ' . Locale::spell('ZIP') . ' or ' . Lang::write('MLS') . ' Number';
        }
    }
}
