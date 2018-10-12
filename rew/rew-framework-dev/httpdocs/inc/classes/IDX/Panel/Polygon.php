<?php

/**
 * Polygon Search
 * @package IDX_Panel
 */
class IDX_Panel_Polygon extends IDX_Panel implements IDX_Panel_Interface_Taggable
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Polygon Search';

    /**
     * Tooltip Message
     * @var string
     */
    protected $tooltip = 'Click on the map to draw your polygon search.';

    /**
     * Polygon control id
     * @var string
     */
    protected $control_id = 'GPolygonControl';

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['tooltip'])) {
            $this->tooltip = $options['tooltip'];
        }
        if (!empty($options['control_id'])) {
            $this->control_id = $options['control_id'];
        }
        parent::__construct($options);
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
        return new IDX_Search_Tag('In Polygon', array('polygon' => 1));
    }

    /**
     * @see IDX_Panel::getValue
     * @return string|NULL
     */
    public function getValue()
    {
        $value = $_REQUEST['map']['polygon'];
        $polygon = json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (!empty($polygon) && is_array($polygon)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {

        // Current value
        $value = $this->getValue();

        // Generate Markup
        $html = '<div id="' . $this->control_id . '"></div>';
        if (!empty($this->tooltip)) {
            $html .= '<small class="tip ' . $this->hiddenClass . '">' . $this->tooltip . '</small>';
        }
        $html .= '<input type="hidden" name="map[polygon]" value="' . Format::htmlspecialchars($value) . '">';

        // Return HTML
        return $html;
    }

    /**
     * Get Tooltip Text
     * @return $string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * Get Control ID
     * @return $string
     */
    public function getControlId()
    {
        return $this->control_id;
    }
}
