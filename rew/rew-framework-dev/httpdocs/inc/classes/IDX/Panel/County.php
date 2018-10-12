<?php

/**
 * Search by County
 * @package IDX_Panel
 */
class IDX_Panel_County extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'County';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_county';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'AddressCounty';
}
