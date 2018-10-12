<?php

/**
 * Bank Owned
 * @package IDX_Panel
 */
class IDX_Panel_Bankowned extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Bank Owned';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_bankowned';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'IsBankOwned';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'title' => 'No Preference'),
            array('value' => 'Y', 'title' => 'Search Bank Owned'),
            array('value' => 'N', 'title' => 'Exclude Bank Owned')
        );
    }
}
