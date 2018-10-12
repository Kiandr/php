<?php

/**
 * Waterfront
 * @package IDX_Panel
 */
class IDX_Panel_Waterfront extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Waterfront';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_waterfront';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'IsWaterfront';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'title' => 'No Preference'),
            array('value' => 'Y', 'title' => 'Search Waterfront'),
            array('value' => 'N', 'title' => 'Exclude Waterfront')
        );
    }
}
