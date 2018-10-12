<?php

/**
 * Range
 * @package IDX_Panel
 */
class IDX_Panel_Type_Range extends IDX_Panel implements IDX_Panel_Interface_Taggable
{
    use IDX_Panel_Trait_TypedMarkup;

    const PANEL_TYPE = 'range';

    /**
     * Min. Input Name
     * @var string
     */
    protected $minInput;

    /**
     * Max. Input Name
     * @var string
     */
    protected $maxInput;

    /**
     * Min. Input Class Name
     * @var string
     */
    protected $minClass;

    /**
     * Max. Input Class Name
     * @var string
     */
    protected $maxClass;

    /**
     * Min. Input Placeholder Option
     * @var string
     */
    protected $minOption = 'Min';

    /**
     * Max. Input Placeholder Option
     * @var string
     */
    protected $maxOption = 'Max';

    /**
     * Show Min. Input
     * @var boolean
     */
    protected $showMin = true;

    /**
     * Show Max. Input
     * @var boolean
     */
    protected $showMax = true;

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['minInput'])) {
            $this->minInput = $options['minInput'];
        }
        if (isset($options['maxInput'])) {
            $this->maxInput = $options['maxInput'];
        }
        if (isset($options['minClass'])) {
            $this->minClass = $options['minClass'];
        }
        if (isset($options['maxClass'])) {
            $this->maxClass = $options['maxClass'];
        }
        if (isset($options['showMin'])) {
            $this->showMin  = $options['showMin'];
        }
        if (isset($options['showMax'])) {
            $this->showMax  = $options['showMax'];
        }
        if (isset($options['minOption'])) {
            $this->minOption = $options['minOption'];
        }
        if (isset($options['maxOption'])) {
            $this->maxOption = $options['maxOption'];
        }
        parent::__construct($options);
    }

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
        $minOption = $this->getOptionTitle($minValue);
        $maxOption = $this->getOptionTitle($maxValue);
        if (!empty($minOption) && !empty($maxOption)) {
            $tags[] = new IDX_Search_Tag($minOption . ' - ' . $maxOption, $value);
        } else if (!empty($minOption)) {
            $tags[] = new IDX_Search_Tag('More than ' . $minOption, array($minInput => $minValue));
        } else if (!empty($maxOption)) {
            $tags[] = new IDX_Search_Tag('Less than ' . $maxOption, array($maxInput => $maxValue));
        }
        return $tags;
    }

    /**
     * @see IDX_Panel::getValue
     * @return array
     */
    public function getValue()
    {
        return array_filter(array(
            $this->minInput => $_REQUEST[$this->minInput],
            $this->maxInput => $_REQUEST[$this->maxInput]
        ));
    }

    /**
     * Get Input Names
     * @return array
     */
    public function getInputs()
    {
        return array($this->minInput, $this->maxInput);
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getBrewMarkup()
    {
        $min = $this->showMin ? $this->getMinMarkup() : false;
        $max = $this->showMax ? $this->getMaxMarkup() : false;
        if ($min && $max) {
            return '<div class="range">'
                . '<span class="min">' . $min . '</span>'
                . '<span class="tween"> to </span>'
                . '<span class="max">' . $max . '</span>'
            . '</div>';
        } elseif ($min) {
            return $min;
        } elseif ($max) {
            return $max;
        }
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getUikitMarkup()
    {
        $min = $this->showMin ? $this->getMinMarkup() : false;
        $max = $this->showMax ? $this->getMaxMarkup() : false;

        if ($min && $max) {
            return '<div class="range">'
            . '<span class="min uk-display-inline-block">' . $min . '</span>'
            . '<span class="tween uk-display-inline-block"> to </span>'
            . '<span class="max uk-display-inline-block">' . $max . '</span>'
            . '</div>';
        } elseif ($min) {
            return $min;
        } elseif ($max) {
            return $max;
        }
    }

    /**
     * Get Options for Min. Range Input
     * @return array
     */
    public function getMinOptions()
    {
        return array_merge(array(
            array('value' => '', 'title' => $this->minOption)
        ), $this->getOptions());
    }

    /**
     * Get Options for Max. Range Input
     * @return array
     */
    public function getMaxOptions()
    {
        return array_merge(array(
            array('value' => '', 'title' => $this->maxOption)
        ), $this->getOptions());
    }

    /**
     * Get HTML Markup for Min. Range Input
     * @param string $attrs Extra attributes for <select>
     * @return string
     */
    public function getMinMarkup($attrs = null)
    {
        $value = $_REQUEST[$this->minInput];
        $class = !empty($this->minClass) ? ' class="' . htmlspecialchars($this->minClass) . '"' : '';
        $html = '<select id="' . $this->minInput . '" name="' . $this->minInput . '"' . $class . (!empty($attrs) ? ' ' . $attrs : '') . '>';
        $options = $this->formatOptions($this->getMinOptions());
        foreach ($options as $option) {
            $selected = (is_array($value) && in_array($option['value'], $value)) ||  (is_string($value) && $option['value'] == $value) ? ' selected' : '';
            $html .= '<option value="' . $option['value'] . '"' . $selected . '> ' . $option['title'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Get HTML Markup for Max. Range Input
     * @param string $attrs Extra attributes for <select>
     * @return string
     */
    public function getMaxMarkup($attrs = null)
    {
        $value = $_REQUEST[$this->maxInput];
        $class = !empty($this->maxClass) ? ' class="' . htmlspecialchars($this->maxClass) . '"' : '';
        $html = '<select id="' . $this->maxInput . '" name="' . $this->maxInput . '"' . $class . (!empty($attrs) ? ' ' . $attrs : '') . '>';
        $options = $this->formatOptions($this->getMaxOptions());
        foreach ($options as $option) {
            $selected = (is_array($value) && in_array($option['value'], $value)) ||  (is_string($value) && $option['value'] == $value) ? ' selected' : '';
            $html .= '<option value="' . $option['value'] . '"' . $selected . '> ' . $option['title'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Get option title by value
     * @param string $value
     * @return string|NULL
     */
    protected function getOptionTitle($value)
    {
        if (empty($value)) {
            return null;
        }
        if ($options = $this->getOptions()) {
            foreach ($options as $option) {
                if ($option['value'] == $value) {
                    return $option['title'];
                }
            }
        }
        return null;
    }

    /**
     * Get Min Input Class Name
     * @return string
     */
    public function getMinClass()
    {
        return $this->minClass;
    }

    /**
     * Get Max Input Class Name
     * @return string
     */
    public function getMaxClass()
    {
        return $this->maxClass;
    }

    /**
     * Get Show Min Input Name
     * @return boolean
     */
    public function getShowMin()
    {
        return $this->showMin;
    }

    /**
     * Get Show Max Input Name
     * @return boolean
     */
    public function getShowMax()
    {
        return $this->showMax;
    }

    /**
     * Get Min Option Value
     * @return string
     */
    public function getMinOption()
    {
        return $this->minOption;
    }

    /**
     * Get Max Option Value
     * @return string
     */
    public function getMaxOption()
    {
        return $this->maxOption;
    }

    /**
     * @return array
     */
    public function typeSpecificJsonSerialize()
    {
        return array_merge(parent::typeSpecificJsonSerialize(),
            [
                'param_name' => [
                    'min' => $this->minInput,
                    'max' => $this->maxInput
                ]
            ]);
    }
}
