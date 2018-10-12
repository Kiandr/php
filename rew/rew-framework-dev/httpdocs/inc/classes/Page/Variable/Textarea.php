<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Textarea
 */
class Page_Variable_Textarea extends Page_Variable
{

    /**
     * Placeholder Text
     * @var string
     */
    protected $placeholder;

    /**
     * Number of rows
     * @var int
     */
    protected $rows = 3;

    /**
     * @see Page_Variable::__construct()
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        if (!empty($options['placeholder'])) {
            $this->setPlaceholder($options['placeholder']);
        }
        if (!empty($options['rows'])) {
            $this->setRows($options['rows']);
        }
    }

    /**
     * Set placeholder text
     * @param string $placeholder
     * @return self
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set # of rows
     * @param int $rows
     * @return self
     */
    public function setRows($rows)
    {
        $this->rows = (int) $rows;
        return $this;
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
     * Get # of rows
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
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

        // # of rows
        $rows = $this->getRows();
        if ($rows > 0) {
            $attrs .= ' rows="' . Format::htmlspecialchars($rows) . '"';
        }

        // Display Title
        $this->displayTitle();

        // Display Field
        echo '<textarea class="w1/1"' . $attrs . $placeholder . $disabled . $required . '>';
        echo Format::htmlspecialchars($this->getValue());
        echo '</textarea>';

        // Display Tooltip
        $this->displayTooltip();
    }
}
