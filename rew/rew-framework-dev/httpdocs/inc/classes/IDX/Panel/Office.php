<?php

/**
 * Search by Office
 * @package IDX_Panel
 */
class IDX_Panel_Office extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Search by Office';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_office';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingOffice';
}
