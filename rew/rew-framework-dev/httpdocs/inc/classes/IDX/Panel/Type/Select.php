<?php

/**
 * Select List
 * @package IDX_Panel
 */
class IDX_Panel_Type_Select extends IDX_Panel implements IDX_Panel_Interface_Taggable
{

    const PANEL_TYPE = 'select';

    /**
     * Input Class
     * @var string
     */
    protected $inputClass = 'x12';

    /**
     * Multiple
     * @var bool|null
     */
    protected $multiple;

    /**
     * Size
     * @var int|null
     */
    protected $size;

    /**
     * Placeholder Option
     * @var string
     */
    protected $placeholder = 'No Preference';

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['placeholder'])) {
            $this->placeholder = $options['placeholder'];
        }
        if (isset($options['multiple'])) {
            $this->multiple = $options['multiple'];
        }
        if (isset($options['size'])) {
            $this->size = $options['size'];
        }
        parent::__construct($options);
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {
        $value = $this->getValue();
        $class = !empty($this->inputClass) ? ' class="' . htmlspecialchars($this->inputClass) . '"' : '';
        $size = !empty($this->size) ? ' size="' . htmlspecialchars($this->size) . '"' : '';
        $multiple = !empty($this->multiple) ? ' multiple' : '';
        $html = '<select name="' . $this->inputName . (!empty($multiple) ? '[]' : '') . '"' . $class . $size . $multiple . '>';
        $options = $this->formatOptions($this->getOptions());
        foreach ($options as $option) {
            $selected = (is_array($value) && in_array($option['value'], $value)) ||  (is_string($value) && $option['value'] == $value) ? ' selected' : '';
            $html .= '<option value="' . $option['value'] . '"' . $selected . '> ' . $option['title'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        return array_map(function ($value) {
            return new IDX_Search_Tag(
                $this->getOptionTitle($value),
                array($this->inputName => $value)
            );
        }, $this->getValues());
    }

    /**
     * Add 'No Preference' Option to Top of List
     * @see IDX_Panel::getOptions()
     */
    public function getOptions()
    {
        if (!empty($this->multiple)) {
            $this->options = parent::getOptions();
        } else {
            $this->options = parent::getOptions();
            if (!empty($this->placeholder)) {
                $this->options = array_merge(array(
                    array('value' => '', 'title' => $this->placeholder)
                ), $this->options);
            }
        }
        return $this->options;
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
     * Set Placeholder Text
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Get Placeholder Text
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Return Is Multiple
     * @return boolean|NULL
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Return Select Size
     * @return boolean|NULL
     */
    public function getSize()
    {
        return $this->size;
    }
}
