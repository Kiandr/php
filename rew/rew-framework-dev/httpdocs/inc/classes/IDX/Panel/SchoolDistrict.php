<?php

/**
 * School District Search
 * @package IDX_Panel
 */
class IDX_Panel_SchoolDistrict extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'School District';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'school_district';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete single location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'SchoolDistrict';
}
