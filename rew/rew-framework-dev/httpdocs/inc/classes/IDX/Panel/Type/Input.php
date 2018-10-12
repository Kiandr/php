<?php

/**
 * Text Input
 * @package IDX_Panel
 */
class IDX_Panel_Type_Input extends IDX_Panel implements IDX_Panel_Interface_Taggable
{
    const PANEL_TYPE = 'text';

    /**
     * Class Name for Input Field
     * @var string
     */
    protected $inputClass = 'x12';

    /**
     * Placeholder Text
     * @var string
     */
    protected $placeholder = '...';

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['placeholder'])) {
            $this->placeholder = $options['placeholder'];
        }
        parent::__construct($options);
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {
        $value = $this->stringify($this->getValue());
        $class = !empty($this->inputClass) ? ' class="' . htmlspecialchars($this->inputClass) . '"' : '';
        $placeholder = !empty($this->placeholder) ? ' placeholder="' . $this->placeholder . '"' : '';
        return '<input name="' . $this->inputName . '" value="' . htmlspecialchars($value) . '"' . $placeholder . $class . '>';
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag[]
     */
    public function getTags()
    {
        $value = $this->getValue();
        if (empty($value)) {
            return null;
        }
        $value = $this->stringify($this->getValue());
        return new IDX_Search_Tag($this->getTitle() . ': ' . $value, array($this->inputName => $value));
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
     * @return string $placeholder
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Return value as string
     * @param array|string $value
     * @uses Format::trim
     * @return string
     */
    protected function stringify($value)
    {
        $value = is_array($value) ? implode(', ', Format::trim($value)) : $value;
        return rtrim($value, ', ');
    }
}
