<?php

/**
 * Search by Zip Code
 * @package IDX_Panel
 */
class IDX_Panel_Zip extends IDX_Panel_Type_Input
{

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_zip';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'AddressZipCode';

    /**
     * Set Panel Title on Construct (Because It's Dynamic)
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->title = Locale::spell('Zip Code');
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        return array_map(function ($value) {
            return new IDX_Search_Tag(
                Locale::spell('Zip Code') . ': ' . $value,
                array($this->inputName => $value)
            );
        }, $this->getValues());
    }
}
