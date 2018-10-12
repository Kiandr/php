<?php

/**
 * Search by GulfAccess
 * @package IDX_Panel
 */
class IDX_Feed_RAGFMB_Panel_GulfAccess extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    public $title = 'Gulf Access';

    /**
     * Input Name
     * @var string
     */
    public $inputName = 'search_gulfaccess';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'IsGulfAccess';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '',  'title' => 'No Preference'),
            array('value' => 'Y', 'title' => 'Search Gulf Access'),
            array('value' => 'N', 'title' => 'Exclude Gulf Access')
        );
    }
}
