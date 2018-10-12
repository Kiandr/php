<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_IDX
 */
class Page_Variable_IDX extends Page_Variable_Select
{

    /**
     * IDX Panel
     * @var string
     */
    protected $panel;

    /**
     * @see Page_Variable::__construct()
     */
    public function __construct($name, $options = array())
    {

        // Set IDX Panel
        if (!empty($options['panel'])) {
            $this->setPanel($options['panel']);
        }

        // Parent Constructor
        parent::__construct($name, $options);
    }

    /**
     * Get Valid Options
     * @return array
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->options = array();
            $panel = IDX_Panel::get($this->getPanel(), array('placeholder' => false));
            if (!empty($panel)) {
                $this->options = $panel->getOptions();
            }
        }
        return $this->options;
    }

    /**
     * Get IDX Panel
     * @return string
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * Set IDX Panel
     * @param string $panel
     * @return self
     */
    public function setPanel($panel)
    {
        $this->panel = $panel;
        return $this;
    }

    /**
     * @see Page_Variable_Select::display
     */
    public function display(PageInterface $page = null, $disabled = false)
    {

        // Default display
        echo '<div id="' . $this->getId() . '" class="var-' . $this->getType() . '">';
        parent::display($page, $disabled);
        echo '</div>';
    }
}
