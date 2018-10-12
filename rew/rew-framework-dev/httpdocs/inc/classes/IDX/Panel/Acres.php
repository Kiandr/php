<?php

/**
 * Acres Range
 * @package IDX_Panel
 */
class IDX_Panel_Acres extends IDX_Panel_Type_Range
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Lot Size';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'NumberOfAcres';

    /**
     * Min. Input Name
     * @var string
     */
    protected $minInput = 'minimum_acres';

    /**
     * Max. Input Name
     * @var string
     */
    protected $maxInput = 'maximum_acres';

    /**
     * Available Options
     * @var array
     */
    protected $options = array(
        array('value' => 0.25, 'title' => '1/4 Acre'),
        array('value' => 0.50, 'title' => '1/2 Acre'),
        array('value' => 1.00, 'title' => '1 Acre'),
        array('value' => 1.50, 'title' => '1 1/2 Acre'),
        array('value' => 2.00, 'title' => '2 Acres'),
        array('value' => 2.50, 'title' => '2 1/2 Acres'),
        array('value' => 3.00, 'title' => '3 Acres'),
        array('value' => 5.00, 'title' => '5 Acres'),
        array('value' => 10.00, 'title' => '10 Acres'),
        array('value' => 15.00, 'title' => '15 Acres'),
        array('value' => 20.00, 'title' => '20 Acres'),
        array('value' => 25.00, 'title' => '25 Acres'),
        array('value' => 30.00, 'title' => '30 Acres'),
        array('value' => 40.00, 'title' => '40 Acres'),
        array('value' => 50.00, 'title' => '50 Acres'),
        array('value' => 100.00, 'title' => '100 Acres')
    );
}
