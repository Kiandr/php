<?php

/**
 * Search by Agent
 * @package IDX_Panel
 */
class IDX_Panel_Agent extends IDX_Panel_Type_Input
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Search by Agent';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_agent';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 autocomplete';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'ListingAgent';
}
