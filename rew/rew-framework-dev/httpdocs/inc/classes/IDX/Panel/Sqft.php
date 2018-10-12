<?php

/**
 * Square Feet Range
 * @package IDX_Panel
 */
class IDX_Panel_Sqft extends IDX_Panel_Type_Range
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Property Size';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'NumberOfSqFt';

    /**
     * Min. Input Name
     * @var string
     */
    protected $minInput = 'minimum_sqft';

    /**
     * Max. Input Name
     * @var string
     */
    protected $maxInput = 'maximum_sqft';

    /**
     * Generate Available Options
     * @see IDX_Panel::getOptions()
     */
    public function getOptions()
    {
        if (isset($this->options)) {
            return $this->options;
        }
        $this->options = array();
        for ($sqft_min = 500; $sqft_min <= 10000; $sqft_min += 500) {
            $this->options[] = array('value' => $sqft_min, 'title' => number_format($sqft_min).' ft&sup2;');
        }
        return $this->options;
    }
}
