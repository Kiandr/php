<?php

/**
 * Has oenhouse
 * @package IDX_Panel
 */
class IDX_Panel_HasOpenHouse extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Has Open House';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_has_openhouse';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'HasOpenHouse';

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'title' => 'No Preference'),
            array('value' => 'Y', 'title' => 'Search Open Houses'),
            array('value' => 'N', 'title' => 'Exclude Open Houses')
        );
    }
}
