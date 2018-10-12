<?php

use REW\Core\Interfaces\PageInterface;

/**
 * Page Variable
 */
abstract class Page_Variable
{

    /**
     * Page_Variable_Text
     * @var string
     */
    const TYPE_TEXT = 'text';

    /**
     * Page_Variable_Textarea
     * @var string
     */
    const TYPE_TEXTAREA = 'textarea';

    /**
     * Page_Variable_Select
     * @var string
     */
    const TYPE_SELECT = 'select';

    /**
     * Page_Variable_Boolean
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Page_Variable_Image
     * @var string
     */
    const TYPE_IMAGE = 'image';

    /**
     * Page_Variable_Item
     * @var string
     */
    const TYPE_ITEM = 'item';

    /**
     * Page_Variable_List
     * @var string
     */
    const TYPE_LIST = 'list';

    /**
     * Page_Variable_Feed
     * @var unknown
     */
    const TYPE_FEED = 'feed';

    /**
     * Page_Variable_IDX
     * @var unknown
     */
    const TYPE_IDX = 'idx';

    /**
     * Variable Types
     * @var array
     */
    static protected $types = array(
        self::TYPE_TEXT     => 'Page_Variable_Text',
        self::TYPE_TEXTAREA => 'Page_Variable_Textarea',
        self::TYPE_SELECT   => 'Page_Variable_Select',
        self::TYPE_BOOLEAN  => 'Page_Variable_Boolean',
        self::TYPE_IMAGE    => 'Page_Variable_Image',
        self::TYPE_ITEM     => 'Page_Variable_Item',
        self::TYPE_LIST     => 'Page_Variable_List',
        self::TYPE_FEED     => 'Page_Variable_Feed',
        self::TYPE_IDX      => 'Page_Variable_IDX',
    );

    /**
     * Page Template
     * @var Page_Template
     */
    protected $template;

    /**
     * Variable Title
     * @var string|false
     */
    protected $title;

    /**
     * Variable Name
     * @var string
     */
    protected $name;

    /**
     * Variable Value
     * @var mixed
     */
    protected $value;

    /**
     * Default Value
     * @var mixed
     */
    protected $default;

    /**
     * Is Required
     * @var boolean
     */
    protected $required;

    /**
     * Tooltip Message
     * @var string
     */
    protected $tooltip;

    /**
     * Parent Variable
     * @var Page_Variable|null
     */
    protected $parent;

    /**
     * Children Variables
     * @var Page_Variable[]|null
     */
    protected $children;

    /**
     * Input name
     * @var string
     */
    protected $inputName;

    /**
     * Element to wrap title
     * @var string
     */
    protected $titleElement = 'label';

    /**
     * Unique ID
     * @var string
     */
    protected $_id;

    /**
     * Enabled settings
     * @var string|array|bool
     */
    protected $_enabled = true;

    /**
     * Variable enabled on condition
     * @var mixed
     */
    protected $dependency = false;

    /**
     * Setup Page Variable
     * @param string $name
     * @param array $options
     */
    protected function __construct($name, $options = array())
    {
        $this->setName($name);
        if (!empty($options) && is_array($options)) {
            // Set variable's parent
            if (isset($options['parent'])) {
                $this->setParent($options['parent']);
            }
            // Variable Enabled
            if (isset($options['_enabled'])) {
                $this->setEnabled($options['_enabled']);
            }
            // Variable Title
            if (isset($options['title'])) {
                $this->setTitle($options['title']);
            }
            // Default Value
            if (isset($options['default'])) {
                $this->setDefault($options['default']);
            }
            // Define input name
            if (!empty($options['inputName'])) {
                $this->inputName = $options['inputName'];
            }
            // Tooltip message
            if (!empty($options['tooltip'])) {
                $this->setTooltip($options['tooltip']);
            }
            // Is Required
            if (!empty($options['required'])) {
                $this->isRequired(true);
            }
            // Has Dependency
            if (!empty($options['dependency'])) {
                $this->setDependency($options['dependency']);
            }
            // Children Variables
            if (!empty($options['children']) && is_array($options['children'])) {
                $this->children = array();
                foreach ($options['children'] as $name => $options) {
                    $this->children[] = self::load($name, array_merge($options, array(
                        'parent' => $this
                    )));
                }
            }
        }
    }

    /**
     * Get type of variable
     * @return string
     */
    public function getType()
    {
        $className = get_class($this);
        return array_search($className, self::$types);
    }

    /**
     * Get Template
     * @return Page_Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get Variable Title (If no title is set, name is returned)
     * @return string|false
     */
    public function getTitle()
    {
        if ($this->title === false) {
            return false;
        }
        if (!empty($this->title)) {
            return $this->title;
        }
        return ucwords($this->name);
    }

    /**
     * Get Tooltip Message
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * Get Variable Name
     * @return string
     */
    public function getName()
    {
        $parent = $this->getParent();
        return (!empty($parent) ? $parent->getName() . '.' : '') . $this->name;
    }

    /**
     * Get Variable Value
     * @param boolean $default If no value set, Return default value
     * @return mixed
     */
    public function getValue($default = true)
    {
        return isset($this->value) ? $this->value : ($default !== false ? $this->getDefault() : null);
    }

    /**
     * Get Default Value
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Generate unique ID
     * @return string
     */
    public function getId()
    {
        if (!$this->_id) {
            $this->_id = str_replace('.', '-', implode('-', array($this->template->getName(), $this->getName())));
            $this->_id .= '-' . mt_rand();
        }
        return $this->_id;
    }

    /**
     * Get Field Name
     * @return string
     */
    public function getField()
    {
        if ($this->inputName) {
            return $this->inputName;
        }
        return 'variables[' . $this->template->getName() . '][' . $this->getName() . ']';
    }

    /**
     * Get Parent Variable
     * @return Page_Variable|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get Children Variables
     * @return Page_Variable[]|null
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get enabled settings
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Check if variable is enabled
     * @return bool
     */
    public function isEnabled()
    {
        $parent = $this->getParent();
        $enabled = $this->getEnabled();
        if (!empty($parent)) {
            $parentValue = $parent->getValue();
            if (is_bool($enabled)) {
                if (is_bool($parentValue)) {
                    return $enabled === $parentValue;
                } else {
                    return $enabled;
                }
            } elseif (is_string($enabled)) {
                return $parentValue == $enabled;
            } elseif (is_array($enabled)) {
                return in_array($parentValue, $enabled);
            }
        }
        // Check other variable parents for enabled.
        if (!empty($enabled) && !is_bool($enabled)) {
            $enabled_values = $enabled;
            $enabled = false;
            // Loop if an array
            if (is_array($enabled_values)) {
                foreach ($enabled_values as $key => $val) {
                    $enabled = $this->searchVariables($val);
                    if ($enabled) {
                        break;
                    }
                }
            } else {
                $enabled = $this->searchVariables($enabled_values);
            }
        }
        return $enabled;
    }

    /**
     * Check if Required or Set as Required
     * @param bool|NULL $required
     * @return bool
     */
    public function isRequired($required = null)
    {
        if (is_bool($required)) {
            $this->required = $required;
        }
        return (bool) $this->required;
    }

    /**
     * Check if variable has a dependency (only enable if dependency met)
     * @return mixed
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * Set Template
     * @param Page_Template $template
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set Variable Title
     * @param string|false $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set Tooltip Message
     * @param string $tooltip
     * @return self
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    /**
     * Set Variable Name
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set Variable Value
     * @param mixed $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set Default Value
     * @param mixed $value
     * @return self
     */
    public function setDefault($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * Set Parent Variable
     * @param Page_Variable $parent
     * @return self
     */
    public function setParent(Page_Variable $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Set enabled settings
     * @param string|array|bool $_enabled
     * @return self
     */
    public function setEnabled($_enabled)
    {
        $this->_enabled = $_enabled;
        return $this;
    }

    /**
     * Set variable's input name
     * @param string $inputName
     * @return self
     */
    public function setInputName($inputName)
    {
        $this->inputName = $inputName;
        return $this;
    }

    /**
     * Set DOM element to wrap title
     * @param string $el
     * @throws InvalidArgumentException
     * @return void
     */
    public function setTitleElement($el)
    {
        $validEl = array('h2', 'label');
        if (!in_array($el, $validEl)) {
            throw new InvalidArgumentException('Value must be one of: ' . implode(', ', $validEl));
        }
        $this->titleElement = $el;
    }

    /**
     * Set variable dependency (only enable if dependency met)
     * @return void
     */
    public function setDependency($dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * Display title text
     * @return void
     */
    public function displayTitle()
    {
        $titleElement = $this->titleElement;
        if ($titleElement == 'label') {
            $titleElement = 'label class="field__label"';
        }
        if ($titleElement && ($title = $this->getTitle())) {
            $required = $this->isRequired() ? ' <em class="required">*</em>' : '';
            echo '<' . $titleElement . '>' . $title . $required . '</' . $titleElement . '>' . PHP_EOL;
        }
    }

    /**
     * Display tooltip message
     * @return void
     */
    public function displayTooltip()
    {
        $tooltip = $this->getTooltip();
        if (!empty($tooltip)) {
            echo '<p class="tip show">' . $tooltip . '</p>';
        }
    }

    /**
     * Display Variable Field for use in HTML Form
     * @param PageInterface $page Page Instance (To write JavaScript/CSS)
     * @param bool $disabled Disable Field
     * @return void
     */
    abstract public function display(PageInterface $page = null, $disabled = false);

    /**
     * Load Page Variable
     * @param string $name
     * @param array $options
     * @throws Exception
     * @return Page_Variable
     */
    public static function load($name, $options = array())
    {
        // Variable Type
        $type = !empty($options['type']) ? $options['type'] : self::TYPE_TEXT;
        $type = self::$types[$type];
        // Unknown Type
        if (empty($type)) {
            throw new Exception('Variable error: Invalid type');
        }
        // Return Variable
        $variable = new $type ($name, $options);
        return $variable;
    }

    /**
     * Search through all of the variables in this associated Template for a value
     *
     * @param   string|array(string)    $search_key     Key(s) for what to find
     * @return  bool        Return true if we found the key and it is set to value, else false
     */
    public function searchVariables($search_key)
    {

        // Get the template and the variables associated
        $template = $this->getTemplate();
        $template_variables = $template->getVariables();

        $seach_bits =  explode('.', $search_key);
        // Only a single key (no child) found
        if (count($seach_bits) == 2 && !empty($template_variables[$seach_bits[0]])) {
            // Get the value from the key
            $template_value = $template_variables[$seach_bits[0]]->getValue();
            // If the value matches our requested value, return true
            if ($template_value == $seach_bits[1]) {
                return true;
            }

        // This has children, we need to progress through the array to find the children
        } else if (count($seach_bits) > 2 && !empty($template_variables[$seach_bits[0]])) {
            // Initiate some variables to progress through the array of children Variables
            $children = false;
            $variable_name = $seach_bits[0];
            $the_child =  $template_variables[$seach_bits[0]];
            $i = 1;

            // Loop through the children variables until we reach the value at the end
            while (empty($children) || !empty($seach_bits[$i])) {
                // Set the variable name that we are searching for how the child name works.
                $variable_name .= '.' . $seach_bits[$i];

                // We are already on the last child in search, so check the value now
                if (empty($seach_bits[$i+1])) {
                    if ($the_child->getValue() == $seach_bits[count($seach_bits) - 1]) {
                        return true;
                    }
                }

                // Attempt to get the next children
                $children = $the_child->getChildren();

                // There are still children of this child and we still want to keep walking
                if (!empty($children)) {
                    // Loop through the children of this variable
                    foreach ($children as $child_key => $child) {
                        // Child found set the next child up to be processed
                        if ($child->getName() == $variable_name) {
                            $the_child = $child;
                            break;
                        }
                    }
                }

                // Increment our walk index
                $i++;
            }
        }

        return false;
    }
}
