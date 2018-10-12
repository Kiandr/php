<?php

/**
 * Search by Office ID
 * @package IDX_Panel
 */
class IDX_Panel_OfficeId extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Search by Office ID';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'office_id';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingOfficeID';
}
