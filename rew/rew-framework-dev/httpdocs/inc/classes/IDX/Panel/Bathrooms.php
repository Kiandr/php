<?php

/**
 * Bathrooms Range
 * @package IDX_Panel
 */
class IDX_Panel_Bathrooms extends IDX_Panel_Type_Range
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Bathrooms';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'NumberOfBathrooms';

    /**
     * Min. Input Name
     * @var string
     */
    protected $minInput = 'minimum_bathrooms';

    /**
     * Max. Input Name
     * @var string
     */
    protected $maxInput = 'maximum_bathrooms';

    /**
     * Available Options
     * @var array
     */
    protected $options = array(
        array('value' => 1, 'title' => '1'),
        array('value' => 2, 'title' => '2'),
        array('value' => 3, 'title' => '3'),
        array('value' => 4, 'title' => '4'),
        array('value' => 5, 'title' => '5'),
        array('value' => 6, 'title' => '6'),
        array('value' => 7, 'title' => '7'),
        array('value' => 8, 'title' => '8')
    );

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]|NULL
     */
    public function getTags()
    {
        $value = $this->getValue();
        if (empty($value)) {
            return null;
        }
        $minInput = $this->minInput;
        $maxInput = $this->maxInput;
        $minValue = $value[$minInput];
        $maxValue = $value[$maxInput];
        $minBaths = $this->getOptionTitle($minValue);
        $maxBaths = $this->getOptionTitle($maxValue);
        if (!empty($minBaths) && !empty($maxBaths)) {
            $tags[] = new IDX_Search_Tag($minBaths . ' - ' . $maxBaths . ' Baths', $value);
        } else if (!empty($minBaths)) {
            $tags[] = new IDX_Search_Tag($minBaths . '+ Baths', array($minInput => $minValue));
        } else if (!empty($maxBaths)) {
            $tags[] = new IDX_Search_Tag($maxBaths . ' or Less Baths', array($maxInput => $maxValue));
        }
        return $tags;
    }
}
