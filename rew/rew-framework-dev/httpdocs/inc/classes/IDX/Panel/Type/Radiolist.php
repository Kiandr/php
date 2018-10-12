<?php

/**
 * Radio List
 * @package IDX_Panel
 */
class IDX_Panel_Type_Radiolist extends IDX_Panel implements IDX_Panel_Interface_Taggable
{
    use IDX_Panel_Trait_TypedMarkup;

    const PANEL_TYPE = 'radiolist';

    /**
     * Returns markup using BREW
     * @return string
     */
    public function getBrewMarkup()
    {
        $value = $this->getValue();
        $class = !empty($this->inputClass) ? ' class="' . htmlspecialchars($this->inputClass) . '"' : '';
        $html = '<div class="toggleset">';
        $options = $this->formatOptions($this->getOptions());
        foreach ($options as $option) {
            $checked = (is_array($value) && in_array($option['value'], $value)) ||  (is_string($value) && $option['value'] == $value) ? ' checked' : '';
            $html .= '<label><input type="radio" name="' . $this->inputName . '" value="' . $option['value'] . '"' . $checked . $class . '> ' . $option['title'] . '</label>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Returns markup using UiKit
     * @return string
     */
    public function getUikitMarkup()
    {
        $value = $this->getValue();
        $class = !empty($this->inputClass) ? ' class="' . htmlspecialchars($this->inputClass) . '"' : '';
        $html = '<div class="fw-compact-form">';
        $options = $this->formatOptions($this->getOptions());
        foreach ($options as $option) {
            $checked = (is_array($value) && in_array($option['value'], $value)) ||  (is_string($value) && $option['value'] == $value) ? ' checked' : '';
            $html .= '<div>';
            $id = Format::slugify($this->inputName . '-' . $option['value']);
            $html .= '<input id="' . $id . '" type="radio" name="' . $this->inputName . '" value="' . $option['value'] . '"' . $checked . $class . '><label for="' . $id . '"> ' . $option['title'] . '</label>';
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * @see IDX_Panel_Interface_Taggable::getTags
     * @return IDX_Search_Tag|NULL
     */
    public function getTags()
    {
        $value = $this->getValue();
        if (empty($value)) {
            return null;
        }
        return new IDX_Search_Tag(
            $value, // @todo: formatOptions?
            array($this->inputName => $value)
        );
    }
}
