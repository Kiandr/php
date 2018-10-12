<?php

/**
 * Search by Area
 * @package IDX_Panel
 */
class IDX_Panel_Area extends IDX_Panel_Type_Select
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Area';

    /**
     * Input Name
     * @var string
     */
    protected $inputName = 'search_area';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12 location';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'AddressArea';
}
