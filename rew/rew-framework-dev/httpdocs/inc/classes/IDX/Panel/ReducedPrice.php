<?php

/**
 * Reduced Price
 * @package IDX_Panel
 */
class IDX_Panel_ReducedPrice extends IDX_Panel_Type_Dynamic
{
    const ALL_TIME_VALUE = '-1000 YEAR';

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Reduced Price';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_reduced_price';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingPriceOld';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Radiolist';

    /**
     * Placeholder value
     * @var string
     */
    protected $placeholder = 'All Properties';

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct(array $options = array())
    {
        $opts = array('placeholder');
        foreach ($opts as $opt) {
            if (isset($options[$opt])) {
                $this->$opt = $options[$opt];
            }
        }
        parent::__construct($options);
    }

    /**
     * Makes a value more human-readable
     * @param string $value
     * @return string
     */
    protected function getLabelForValue($value)
    {
        $value = str_replace('-', '', $value);

        switch (strtolower($value)) {
            case '':
                return $this->placeholder;
            case '1 day':
                return 'Recently Reduced (within 1 Day)';
            case '7 day':
                return 'Reduced This Week';
            case '31 day':
                return 'Reduced This Month';
            case strtolower(str_replace('-', '', static::ALL_TIME_VALUE)):
                return 'Reduced Price';
            default:
                return 'Reduced Price (within ' . ucwords($value) . ')';
        }
    }

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'title' => $this->getLabelForValue('')),
            array('value' => static::ALL_TIME_VALUE, 'title' => $this->getLabelForValue(static::ALL_TIME_VALUE)),
            array('value' => '-1 DAY', 'title' => $this->getLabelForValue('-1 DAY')),
            array('value' => '-7 DAY', 'title' => $this->getLabelForValue('-7 DAY')),
            array('value' => '-31 DAY', 'title' => $this->getLabelForValue('-31 DAY')),
        );
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        return array_map(function ($value) {
            return new IDX_Search_Tag($this->getLabelForValue($value), array($this->inputName => $value));
        }, $this->getValues());
    }

    /**
     * Get Placeholder Title
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }
}
