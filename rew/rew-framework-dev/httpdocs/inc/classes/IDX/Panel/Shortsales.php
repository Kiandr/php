<?php

/**
 * Short Sales
 * @package IDX_Panel
 */
class IDX_Panel_Shortsales extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Short Sales';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_shortsale';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'IsShortSale';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'title' => 'No Preference'),
            array('value' => 'Y', 'title' => 'Search Short Sales'),
            array('value' => 'N', 'title' => 'Exclude Short Sales')
        );
    }
}
