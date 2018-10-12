<?php

/**
 * Days on Website (based on timestamp_created)
 * @package IDX_Panel
 */
class IDX_Panel_Age extends IDX_Panel_Type_Dynamic
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Days on Website';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_new';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'timestamp_created';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Radiolist';

    protected function getLabelForValue($value)
    {
        $value = str_replace('-', '', $value);

        switch (strtolower($value)) {
            case '1 day':
                return 'New Listings (1 Day)';
            case '7 day':
                return 'This Week (7 Days)';
            case '31 day':
                return 'This Month (31 Days)';
            default:
                return 'New Listings (' . ucwords($value) . ')';
        }
    }

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => 0, 'title' => 'All Listings'),
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
}
