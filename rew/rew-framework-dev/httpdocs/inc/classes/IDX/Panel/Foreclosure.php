<?php

/**
 * Foreclosures
 * @package IDX_Panel
 */
class IDX_Panel_Foreclosure extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Foreclosures';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_foreclosure';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'IsForeclosure';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'title' => 'No Preference'),
            array('value' => 'Y', 'title' => 'Search Foreclosures'),
            array('value' => 'N', 'title' => 'Exclude Foreclosures')
        );
    }
}
