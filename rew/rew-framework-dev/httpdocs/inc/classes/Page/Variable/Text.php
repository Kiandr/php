<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Text
 */
class Page_Variable_Text extends Page_Variable
{

    /**
     * Placeholder Text
     * @var string
     */
    protected $placeholder;

    /**
     * @see Page_Variable::__construct()
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        if (!empty($options['placeholder'])) {
            $this->setPlaceholder($options['placeholder']);
        }
    }

    /**
     * Set placeholder text
     * @param string $placeholder
     * @return void
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Get placeholder text
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @see Page_Variable::display()
     */
    public function display(PageInterface $page = null, $disabled = false)
    {

        // Field Attributes
        $attrs = ' data-var="' . $this->getName() . '" name="' . $this->getField() . '"';

        // Disabled Field
        $disabled = !empty($disabled) ? ' disabled' : '';

        // Required Field
        $required = $this->isRequired() ? ' required' : '';

        // Placeholder Text
        if ($placeholder = $this->getPlaceholder()) {
            $placeholder = ' placeholder="' . Format::htmlspecialchars($placeholder) . '"';
        }

        // Display Title
        $this->displayTitle();

        // Display Field
        echo '<input class="w1/1" ' . $attrs . ' value="' . Format::htmlspecialchars($this->getValue()) . '"' . $placeholder . $disabled . $required . '>' . PHP_EOL;

        // Display Tooltip
        $this->displayTooltip();
    }
}
