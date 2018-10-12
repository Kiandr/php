<?php

/**
 * Days on Website
 * @package IDX_Panel
 */
class IDX_Panel_Dow extends IDX_Panel_Type_Dynamic
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
    protected $inputName = 'maximum_dow';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingDOW';

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
}
