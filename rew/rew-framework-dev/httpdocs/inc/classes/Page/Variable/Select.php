<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page_Variable_Select
 */
class Page_Variable_Select extends Page_Variable
{

    /**
     * Valid Options
     * @var array
     */
    protected $options;

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

        // Valid Options
        if (!empty($options['options'])) {
            $this->setOptions($options['options']);
        }

        // Set Placeholder
        if (!empty($options['placeholder'])) {
            $this->setPlaceholder($options['placeholder']);
        }

        // Parent Constructor
        parent::__construct($name, $options);
    }

    /**
     * Get Valid Options
     * @param PageInterface $page Page Instance (To check for disabled)
     * @return array
     */
    public function getOptions(PageInterface $page = null)
    {
                
        return array_filter($this->options, function ($option) use ($page) {
            
            // Match dynamic options
            preg_match('/\{(\!?info|\!?variable|\!?module|\!?request)\.([A-Za-z\-\_\.]+)\}/', $option['disabled'], $match);
            if (!empty($match)) {
                // {!inverse} match
                $inverse = (substr($match[1], 0, 1) === '!');
                if (!empty($inverse)) {
                    $match[1] = substr($match[1], 1);
                }
        
                // Page Information
                if ($page && $match[1] === 'info') {
                    $value = $page->info($match[2]);
    
                // Page Variable
                } else if ($page && $match[1] === 'variable') {
                    // Old code didn't work cause $page is not currently setup with it's variables on the backend
                    if ($page->getSkin()->getName() == 'Backend') {
                        $value = $this->searchVariables($match[2]);
                        
                    // Old code to be used on frontend
                    } else {
                        $value = $page->variable($match[2]);
                    }
    
                // $_REQUEST variable
                } else if ($match[1] === 'request') {
                    $value = $_REQUEST[$match[2]];
    
                // Installed module
                } else if ($match[1] === 'module') {
                    $value = Settings::getInstance()->MODULES[$match[2]];
                }
    
                // NULL means no
                if (is_null($value)) {
                    $value = false;
                }
    
                // Yes means no
                if ($inverse) {
                    $value = !$value;
                }
            }
            
            return isset($value) ? empty($value) : empty($option['disabled']);
        });
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
     * Set Valid Options
     * @param array $options
     * @throws PDOException
     * @return self
     */
    public function setOptions($options)
    {
        if (!empty($options['query'])) {
            try {
                $query = DB::get()->prepare($options['query']);
                $query->execute();
                $options = $query->fetchAll();
            } catch (PDOException $e) {
                throw $e;
            }
        }
        $this->options = $options;
        return $this;
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

        // Current value
        $value = $this->getValue();

        // Available Options
        $options = $this->getOptions($page);

        // Placeholder Text
        $placeholder = $this->getPlaceholder();

        // Display Title
        $this->displayTitle();

        // Display Field
        echo '<select class="w1/1"' . $attrs . $disabled . $required . '>' . PHP_EOL;
        if (empty($required) || !empty($placeholder)) {
            echo '<option value="">' . $placeholder . '</option>' . PHP_EOL;
        }
        if (!empty($options) && is_array($options)) {
            foreach ($options as $option) {
                $disabled = false;
                if (!empty($option['disabled'])) {
                    preg_match('/\{(\!?variable)\.([A-Za-z\-\_\.]+)\}/', $option['disabled'], $match);
                    if (!empty($match)) {
                        // {!inverse} match
                        $inverse = (substr($match[1], 0, 1) === '!');
                        if (!empty($inverse)) {
                            $match[1] = substr($match[1], 1);
                        }
                        
                        // Yes means no
                        if ($inverse) {
                            $match[2] = '!' . $match[2];
                        }
                        
                        $disabled = $match[2];
                    }
                }
                $selected = ($option['value'] == $value) ? ' selected' : '';
                echo '<option value="' . Format::htmlspecialchars($option['value']) . '"' . $selected . (!empty($disabled) ? ' data-disabled="' . $disabled . '"' : '') . '>' . Format::htmlspecialchars($option['title']) . '</option>' . PHP_EOL;
            }
        }
        echo '</select>' . PHP_EOL;

        // Display Tooltip
        $this->displayTooltip();
    }
}
