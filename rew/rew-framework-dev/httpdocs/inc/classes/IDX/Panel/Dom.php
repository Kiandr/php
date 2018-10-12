<?php

/**
 * Days on Market
 * @package IDX_Panel
 */
class IDX_Panel_Dom extends IDX_Panel_Type_Dynamic
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Days on Market';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'maximum_dom';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingDOM';

    /**
     * Field Type
     * @var string
     */
    protected $fieldType = 'Radiolist';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => 0, 'title' => 'All Listings'),
            array('value' => 1, 'title' => 'New Listings (1 Day)'),
            array('value' => 7, 'title' => 'This Week (7 Days)'),
            array('value' => 31, 'title' => 'This Month (31 Days)')
        );
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag|NULL
     */
    public function getTags()
    {
        $value = $this->getValue();
        if (empty($value)) {
            return null;
        }
        return new IDX_Search_Tag(
            'Less than ' . $value . ' ' . strtolower($this->getTitle()),
            array($this->inputName => $value)
        );
    }
}
