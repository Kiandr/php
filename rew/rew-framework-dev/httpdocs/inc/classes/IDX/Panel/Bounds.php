<?php

/**
 * Search in Bounds
 * @package IDX_Panel
 */
class IDX_Panel_Bounds extends IDX_Panel implements IDX_Panel_Interface_Taggable
{

    /**
     * Panel Title
     * @var string
     */
    protected $title = 'Search in Bounds';

    /**
     * Tooltip Message
     * @var string
     */
    protected $tooltip = 'Search only mappable listings within the map\'s bounds.';

    /**
     * @see IDX_Panel::__construct()
     */
    public function __construct($options = array())
    {
        if (isset($options['tooltip'])) {
            $this->tooltip = $options['tooltip'];
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
        return new IDX_Search_Tag('Within Map Boundaries', array('bounds' => 1));
    }

    /**
     * @see IDX_Panel::getValue
     * @return string
     */
    public function getValue()
    {
        return $_REQUEST['map']['bounds'] ?: $_REQUEST['search_bounds'];
    }

    /**
     * @see IDX_Panel::getMarkup()
     */
    public function getMarkup()
    {

        // Is Checked
        $checked = $this->getValue() ? true : false;

        // Generate Markup
        $html  = '<label class="toggle"><input type="checkbox" name="map[bounds]" value="true"' . (!empty($checked) ? ' checked' : '') . '> ' . Format::htmlspecialchars($this->title) . '</label>';
        if (!empty($this->tooltip)) {
            $html .= '<small class="tip' . (empty($checked) ? ' ' . $this->hiddenClass : '') . '">';
            $html .= $this->tooltip;
            if ($_GET['load_page'] == 'search_map') {
                $html .= 'The search results will be updated as you drag &amp; zoom the map.';
            }
            $html .= '</small>';
        }
        $html .= '<input type="hidden" name="map[ne]" value="' . Format::htmlspecialchars($_REQUEST['map']['ne']) . '">';
        $html .= '<input type="hidden" name="map[sw]" value="' . Format::htmlspecialchars($_REQUEST['map']['sw']) . '">';

        // Return HTML
        return $html;
    }

    /**
     * Return Tooltip Message
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }
}
