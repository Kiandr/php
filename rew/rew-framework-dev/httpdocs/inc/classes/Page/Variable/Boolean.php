<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Boolean
 */
class Page_Variable_Boolean extends Page_Variable
{

    /**
     * @see Page_Variable::getValue()
     */
    public function getValue($default = true)
    {
        return (bool) parent::getValue($default);
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

        // Current Value
        $value = $this->getValue();

        // Unique Field ID
        $id = $this->getId();

        // Display Title
        $this->displayTitle();

        // Display Field
        echo '<div class="toggle">' . PHP_EOL;
        echo '<input type="radio"' . $attrs . ' id="' . $id . '_on" value="1"' . (!empty($value) ? ' checked' : '') . $disabled . '>' . PHP_EOL;
        echo '<label class="toggle__label" for="' . $id . '_on">On</label>' . PHP_EOL;
        echo '<input type="radio"' . $attrs . ' id="' . $id . '_off" value="0"' . (empty($value) ? ' checked' : '') . $disabled . '>' . PHP_EOL;
        echo '<label class="toggle__label" for="' . $id . '_off">Off</label>' . PHP_EOL;
        echo '</div>' . PHP_EOL;

        // Display Tooltip
        $this->displayTooltip();
    }
}
