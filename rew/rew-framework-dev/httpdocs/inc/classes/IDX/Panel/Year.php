<?php

/**
 * Year Built Range
 * @package IDX_Panel
 */
class IDX_Panel_Year extends IDX_Panel_Type_Range
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Year Built';

    /**
     * IDX Field
     * @var string
     */
    protected $field = 'YearBuilt';

    /**
     * Min. Input Name
     * @var string
     */
    protected $minInput = 'minimum_year';

    /**
     * Max. Input Name
     * @var string
     */
    protected $maxInput = 'maximum_year';

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
        $minYear = $this->getOptionTitle($minValue);
        $maxYear = $this->getOptionTitle($maxValue);
        if (!empty($minYear) && !empty($maxYear)) {
            $tags[] = new IDX_Search_Tag('Built between ' . $minYear . ' - ' . $maxYear, $value);
        } else if (!empty($minYear)) {
            $tags[] = new IDX_Search_Tag('Built after ' . $minYear, array($minInput => $minValue));
        } else if (!empty($maxYear)) {
            $tags[] = new IDX_Search_Tag('Built before ' . $maxYear, array($maxInput => $maxValue));
        }
        return $tags;
    }

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
        $cur_year = $this->getCurrentYear();
        $d = (int) floor($cur_year / 10) * 10;
        $diff = $cur_year - $d;
        $d = ($diff <= 5) ? $d-6 : $d;
        for ($i = 1940; $i <= $d; $i += 10) {
            $this->options[] = array('value' => $i, 'title' => $i);
        }
        for ($i = $d + 1; $i <= $cur_year; $i++) {
            $this->options[] = array('value' => $i, 'title' => $i);
        }
        return $this->options;
    }

    /**
     * Get the Current Year with format Y
     * @return false|string
     */
    protected function getCurrentYear()
    {
        return date('Y');
    }
}
