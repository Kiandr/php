<?php

/**
 * IDX Search Panel
 * @package IDX_Panel
 */
abstract class IDX_Panel implements \JsonSerializable
{

    const PANEL_TYPE = '__NOT_SET';

    /**
     * Panel Title
     * @var string
     */
    protected $title;

    /**
     * @var mixed
     */
    protected $placeholder;

    /**
     * DOM Element Type for Title
     * @var string
     */
    protected $titleElement = 'label';

    /**
     * DOM Element Type for Container
     * @var string|null
     */
    protected $containerElement = null;

    /**
     * Class to apply to hide elements
     * @var string
     */
    protected $hiddenClass = 'hidden';

    /**
     * Classes to apply to title wrapper
     * @var string
     */
    protected $titleClasses = '';

    /**
     * Classes to apply to details wrapper
     * @var string
     */
    protected $detailsClass = 'details';

    /**
     * Show Title
     * @var string
     */
    protected $showTitle = true;

    /**
     * Input Name
     * @var string
     */
    protected $inputName;

    /**
     * Input Class
     * @var string
     */
    protected $inputClass;

    /**
     * Panel Class
     * @var string
     */
    protected $panelClass;

    /**
     * IDX Field
     * @var string
     */
    protected $field;

    /**
     * IDX Where
     * @var string|null
     */
    protected $where;

    /**
     * IDX Order
     * @var string|null
     */
    protected $order;

    /**
     * Display Panel
     * @var bool
     */
    protected $display = true;

    /**
     * Allow Toggle
     * @var bool
     */
    protected $toggle = true;

    /**
     * Collapsed / Closed
     * @var bool
     */
    protected $closed = false;

    /**
     * Hidden Panel
     * @var bool
     */
    protected $hidden = false;

    /**
     * Toggle visibility config
     * @var bool
     */
    protected $hide_visibility_toggle = false;

    /**
     * Panel Mode ('search', 'builder', 'snippet')
     * @var string
     */
    protected $mode = 'search';

    /**
     * Panel Options
     * @var array
     */
    protected $options;

    /**
     * Callback to format options before display
     * @var callable
     */
    protected $formatOptions;

    /**
     * @var int
     */
    protected $formGroup = null;

    /**
     * @var string
     */
    protected $markupStyle = 'brew';

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $locked = false;

    /**
     * @var boolean
     */
    protected $blocked = false;

    /**
     * Use Memcache
     * @var boolean
     */
    public static $useCache = true;

    /**
     * Re-Cache to Memcache
     * @var boolean
     */
    public static $reCache = false;

    /**
     * Create Panel
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (isset($options['id'])) {
            // Id needs to be set before the hook is applied so that dynamic panels
            // are associated with their actual panel id (needed for grouping).
            $this->id = $options['id'];
        }

        // Load IDX Panel Override Settings
        $settings = Hooks::hook(Hooks::HOOK_IDX_PANEL_SETTINGS)->run($this->getId());

        // Override Default IDX Panel Settings As Necessary
        if (!empty($settings) && is_array($settings)) {
            foreach ($settings as $method => $arg) {
                if (is_callable(array($this, $method))) {
                    $reflection = new ReflectionMethod($this, $method);
                    // Only allow hook to call public methods
                    if ($reflection->isPublic()) {
                        $this->$method($arg);
                    }
                }
                if (property_exists($this, $method)) {
                    $this->$method = $arg;
                }
            }
        }

            $opts = array('mode', 'title', 'field', 'display', 'toggle', 'closed', 'hidden', 'options', 'formatOptions', 'inputName', 'inputClass', 'panelClass', 'showTitle', 'titleElement', 'hiddenClass');
        foreach ($opts as $opt) {
            if (isset($options[$opt])) {
                $this->$opt = $options[$opt];
            }
        }

        // Run hook to control newly constructed IDX panel objects
        Hooks::hook(Hooks::HOOK_IDX_PANEL_CONSTRUCT)->run($this);
    }

    /**
     *
     * Close Panel
     * @param boolean $closed
     * @return void
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * Hide Panel
     * @param boolean $hidden
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Display Panel
     * @param boolean $display
     * @return void
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    }

    /**
     *
     * Set if allowed to toggle collapsed
     * @param boolean $toggle
     * @return void
     */
    public function setToggle($toggle)
    {
        $this->toggle = $toggle;
    }

    /**
     *
     * Set type of DOM element to wrap around title
     * @param string $titleElement
     * @return void
     */
    public function setTitleElement($titleElement)
    {
        $this->titleElement = $titleElement;
    }

    /**
     *
     * Set type of DOM element to wrap around panel innards
     * @param string $containerElement
     * @return void
     */
    public function setContainerElement($containerElement)
    {
        $this->containerElement = $containerElement;
    }

    /**
     *
     * Set the classes to set in the title wrapper
     * @param string $titleClasses
     * @return void
     */
    public function setTitleClasses($titleClasses)
    {
        $this->titleClasses = $titleClasses;
    }

    /**
     * Set panel's CSS classes
     * @param string $panelClass
     */
    public function setPanelClass($panelClass)
    {
        $this->panelClass = $panelClass;
    }

    /**
     * Set panel's details CSS classes
     * @param string $detailsClass
     */
    public function setDetailsClass($detailsClass)
    {
        $this->detailsClass = $detailsClass;
    }

    /**
     * Set class required to hide elements
     * @param string $hiddenClass
     */
    public function setHiddenClass($hiddenClass)
    {
        $this->hiddenClass = $hiddenClass;
    }

    /**
     * Set class required to hide elements
     * @param string $inputClass
     */
    public function setInputClass($inputClass)
    {
        if (!empty($this->inputClass)) {
            $inputClass = $this->inputClass . ' ' . $inputClass;
        }
        $this->inputClass = $inputClass;
    }

    /**
     * Set panel's form group
     * @param int $formGroup
     */
    public function setFormGroup($formGroup)
    {
        $this->formGroup = $formGroup;
    }

    /**
     * Set panel's markup style
     * @param string $markupStyle
     */
    public function setMarkupStyle($markupStyle)
    {
        $this->markupStyle = $markupStyle;
    }
    /**
     * Get Closed Setting
     * @return boolean
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Get Display Settings
     * @return boolean
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get Value
     * @return mixed
     */
    public function getValue()
    {
        return $_REQUEST[$this->inputName];
    }

    /**
     * Get Value as Array
     * @return array
     */
    public function getValues()
    {
        $value = $this->getValue();
        $value = Format::trim($value, ', ');
        $value = is_array($value) ? array_filter($value) : $value;
        if (empty($value)) {
            return array();
        }
        return is_array($value) ? $value : array($value);
    }

    /**
     * Get Input Names
     * @return array
     */
    public function getInputs()
    {
        return array($this->inputName);
    }

    /**
     * Return Panel's CSS Classes
     * @return string
     */
    public function getPanelClass()
    {
        return $this->panelClass;
    }

    /**
     * Return Panel's Form Group
     * @return int
     */
    public function getFormGroup()
    {
        return $this->formGroup;
    }

    /**
     * Get Panel Id
     * @return string
     */
    public function getId()
    {
        // Forced id (i.e. dynamic panel)
        if ($this->id !== null) {
            return $this->id;
        }

        $id = preg_split('/([[:upper:]][[:lower:]]+)/', str_replace(
            array(IDX_Feed::getClass() . '_Panel_', 'IDX_Panel_'),
            '', // Replace IDX_(FEED_)Panel_ Prefix
            get_class($this)
        ), null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        $this->id = strtolower(implode('_', $id));

        return $this->id;
    }

    /**
     * Check If Panel is Hidden
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Check if allowed to toggle collapsed
     * @return boolean
     */
    public function getToggle()
    {
        return $this->toggle;
    }

    /**
     * Get Title Element name
     * @return string
     */
    public function getTitleElement()
    {
        return $this->titleElement;
    }

    /**
     * Get Input classes
     * @return string
     */
    public function getInputClass()
    {
        return $this->inputClass;
    }

    /**
     * Get Field Name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Check If Panel is Available
     * @return boolean
     */
    public function isAvailable()
    {
        if (isset($this->field) && ($this->isBlocked() || !$this->checkField($this->field))) {
            return false;
        }
        return true;
    }

    /**
     * Display Search Panel
     * @return void
     */
    public function display()
    {
        if (isset($this->field) && !$this->checkField($this->field)) {
            return;
        }

        // IDX Builder Panel
        if (in_array($this->mode, array('builder', 'snippet'))) {
            // Show Field
            echo '<dl class="panel idx-builder'
                . (!empty($this->panelClass) ? ' ' . $this->panelClass : '')
                . (!empty($this->closed) && !empty($this->toggle) ? ' collapsed' : ' open')
                . (!empty($this->toggle) ? ' toggle' : '')
                . (empty($this->display) ? ' hidden' : '')
                . '" data-panel="' . $this->getId() . '" id="panel-' . $this->getId() . '">'
                . '<input type="hidden" name="panels[' . $this->getId() . '][hidden]" value="' . intval($this->hidden) . '">'
                . '<input type="hidden" name="panels[' . $this->getId() . '][display]" value="' . intval($this->display) . '">'
                . '<input type="hidden" name="panels[' . $this->getId() . '][collapsed]" value="' . intval($this->closed) . '">'
                . '<dt class="trigger">'
                    . '<span class="handle"></span>'
                    . '<span class="ttl">' . htmlspecialchars($this->title) . '</span>'
                    . '<span class="btns btns--compact R">'
                        . '<a class="btn btn--ico btn--ghost" data-panel-action="expand"><svg class="icon icon-cog mar0"><use xlink:href="/backend/img/icos.svg#icon-cog"/></svg></a>'
                        . (!$this->isLocked() ? '<a class="btn btn--ico btn--ghost" data-panel-action="delete"><svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>' : '')
                    . '</span>'
                . '</dt>'
                . '<dt class="content fld fld--ghost">'
                    . '<div id="rs-' . $this->getId() . '">'
                        . (empty($this->hide_visibility_toggle) && $this->mode == 'builder' && !$this->isLocked() ?
                            '<div class="kv kv--vanilla">'
                            . '<span class="k mute">' . __('Visibility') . ':</span>'
                            . '<span class="v">'
                            . '<select data-panel-action="toggle">'
                                . '<option value="display"'   . (empty($this->hidden) && empty($this->closed) ? ' selected' : '') . '>' . __('Visible') . '</option>'
                                . '<option value="collapsed"' . (empty($this->hidden) && !empty($this->closed) ? ' selected' : '') . '>' . __('Collapsed') . '</option>'
                                . '<option value="hidden"'    . (!empty($this->hidden) ? ' selected' : '') . '>' . __('Hidden') . '</option>'
                            . '</select>'
                            . '</span>'
                            . '</div><div class="kv kv--vanilla"><span class="k mute">' . __('Defaults') . ':</span><div class="v">'
                        : '')
                        . $this->getMarkup()
                    . '</div>'
                . '</dd>'
            . '</dl>';

        // IDX Search Panel
        } else {
            // Display Field
            echo '<div id="field-' . $this->getId() . '" class="field'
                . (!empty($this->panelClass) ? ' ' . $this->panelClass : '')
                . (!empty($this->closed) && !empty($this->toggle) ? ' closed' : '')
                . (!empty($this->toggle) ? ' toggle' : '')
                . (!empty($this->hidden) ? ' ' . $this->hiddenClass : '')
            . '">';
            if ($this->containerElement) {
                echo '<' . $this->containerElement . '>';
            }

            // Display Title
            if (!empty($this->showTitle)) {
                if (!empty($this->titleElement)) {
                    echo sprintf(
                        '<%s%s>',
                        $this->titleElement,
                        $this->titleClasses ? sprintf(' class="%s"', Format::htmlspecialchars($this->titleClasses)) : ''
                    );
                }
                echo $this->title;
                if (!empty($this->titleElement)) {
                    echo '</' . $this->titleElement . '>';
                }
            }
            echo '<div class="' . $this->detailsClass . (!empty($this->closed) && !empty($this->toggle) ? ' ' . $this->hiddenClass : '') . '">';
            echo $this->getMarkup();
            echo '</div>';

            if ($this->containerElement) {
                echo '</' . $this->containerElement . '>';
            }

            echo '</div>';
        }
    }

    /**
     * Run format callback on options
     * @param array $options
     * @return array
     */
    public function formatOptions($options)
    {
        if (is_callable($this->formatOptions)) {
            $options = array_map($this->formatOptions, $options);
        }
        return $options;
    }

    /**
     * Return format callable
     * @return callable
     */
    public function getFormatOptions()
    {
        return $this->formatOptions;
    }

    /**
     * Load Available Options
     * @return array
     */
    public function getOptions()
    {

        // Options Already Loaded
        if (isset($this->options)) {
            return $this->options;
        }

        // IDX Field Not Set
        if (!isset($this->field)) {
            return array();
        }

        // Fetch & Return Available Option
        return $this->options = static::fetchOptions($this->field, $this->where, $this->order);
    }

    /**
     * Set lock
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = !empty($locked);
    }

    /**
     * If true, the panel cannot be removed from the IDX Builder.
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Set block
     * @param boolean $blocked
     */
    public function setBlocked($blocked)
    {
        $this->blocked = !empty($blocked);
    }

    /**
     * If true, the panel cannot be used as an IDX panel.
     * @return boolean
     */
    public function isBlocked()
    {
        return $this->blocked;
    }

    /**
     * Return whether to show the title
     * @return bool
     */
    public function showTitle()
    {
        return $this->showTitle;
    }

    /**
     * Fetch Options from IDX Feed
     * @param string $field IDX Field Name
     * @param string $where Append SQL to WHERE
     * @param string $order SQL ORDER BY
     * @return array
     * @TODO Move this to IDX_Feed
     */
    public static function fetchOptions($field, $where = null, $order = null)
    {

        // IDX Feed
        $idx = Util_IDX::getIdx();
        $db_idx = Util_IDX::getDatabase();

        // Build SELECT Query
        $table = $idx->getTable();
        $field = $idx->field($field);

        // Build WHERE Clause
        $sql_where = "`" . $field . "` != '' AND `" . $field . "` IS NOT NULL"
            . (!empty($where) ? " AND " . $where : "");

        // Any global criteria
        $idx->executeSearchWhereCallback($sql_where);

        $query ="SELECT DISTINCT `" . $field . "` AS `title`"
            . " FROM `" .$table . "`"
            . " WHERE " . $sql_where
            . " GROUP BY `" . $field . "`"
            . " ORDER BY " . (!empty($order) ? $order . "," : "")
            . "`" . $field . "` ASC"
        . ";";

        // Cache Index
        $index = __METHOD__ . ':' . $idx->getName() . ':' . $db_idx->db() . ':' . $table . ':' . md5($query);

        // Is Cached (Server-Wide)
        $options = static::$useCache && !static::$reCache ? Cache::getCache($index, true) : null;
        if (!is_array($options)) {
            // Load Options
            $options = array();
            if ($result = $db_idx->query($query)) {
                while ($option = $db_idx->fetchArray($result)) {
                    $options[] = array ('value' => $option['title'], 'title' => ucwords(strtolower($option['title'])));
                }
            }

            // Save Cache (Server-Wide)
            if (static::$useCache || static::$reCache) {
                Cache::setCache($index, $options, true);
            }
        }

        // Return Options
        return $options;
    }

    /**
     * Check Field Values
     * @param string $field IDX Field Name
     * @return boolean
     * @TODO Move this to IDX_Feed
     */
    public static function checkField($field)
    {

        // IDX Feed
        $idx = Util_IDX::getIdx();
        $db_idx = Util_IDX::getDatabase();

        // IDX Field
        $field = $idx->field($field);

        // Require Field
        if (empty($field)) {
            return false;
        }

        // Cache Key
        $index = __METHOD__ . ':' . $idx->getName() . ':' . $db_idx->db() . ':' . $idx->getTable() . ':' . $field;

        // Is Cached (Server-Wide)
        $cache = static::$useCache && !static::$reCache ? Cache::getCache($index, true) : null;
        if (!is_null($cache)) {
            $check = $cache;

        // Not Cached
        } else {
            // Check for Values
            $check = $db_idx->fetchQuery("SELECT SQL_CACHE `" . $field . "` FROM `" . $idx->getTable() . "` WHERE `" . $field . "` IS NOT NULL AND `" . $field . "` != '' LIMIT 1;");

            // Return Boolean
            $check = !empty($check);

            // Save Cache (Server-Wide)
            if (static::$useCache || static::$reCache) {
                Cache::setCache($index, $check, true);
            }
        }

        // Return Check
        return $check;
    }

    /**
     * Generate HTML Markup
     * @return string
     */
    abstract public function getMarkup();

    /**
     * Get Panel's Class
     * @param string Panel Id
     * @return string|null
     */
    public static function getClass($id)
    {

        // IDX_Feed_Panel
        $id = str_replace(' ', '', ucwords(str_replace('_', ' ', $id)));
        $class = IDX_Feed::getClass() . '_Panel_' . $id;
        if (class_exists($class)) {
            return $class;
        } else {
            // IDX_Panel
            $class = 'IDX_Panel_' . $id;
            if (class_exists($class)) {
                return $class;
            }
        }
        // Not Found
        return null;
    }

    /**
     * Load IDX Panel
     * @param string Panel Id
     * @param array Panel Options
     * @return IDX_Panel|null
     */
    public static function get($id, $options = array())
    {
        $panel = self::getClass($id);
        if (!empty($panel)) {
            return new $panel ($options);
        }
        return null;
    }

    /**
     * Append panels that are missing that should be present in list...
     * @param array $panels
     * @param array $criteria
     * @return void
     */
    public static function displayMissing(&$panels = array(), $criteria = array())
    {
        $defaults = self::defaults();
        foreach ($defaults as $id => $options) {
            // Panel Loaded
            $panel = $panels[$id];
            // Load Panel
            if (!$panel instanceof IDX_Panel) {
                $panel = self::get($id);
            }
            // Check Inputs..
            $inputs = $panel->getInputs();
            foreach ($inputs as $input) {
                // Criteria Set, Show Panel
                if (!empty($criteria[$input])) {
                    // Special Exception (Min. Beds and Min. Baths also exist in Rooms panel)
                    if (in_array($input, array('minimum_bedrooms', 'minimum_bathrooms')) && !empty($panels['rooms'])) {
                        continue;
                    }
                    // Special Exception (Waterfront also exists in Features panel)
                    if ($input === 'search_waterfront' && !empty($panels['features'])) {
                        continue;
                    }
                    // Update Panel
                    if (isset($panels[$panel->getId()])) {
                        if ($panel instanceof IDX_Panel) {
                            $panel->setDisplay(true);
                        } else {
                            $panels[$panel->getId()]['display'] = true;
                        }
                    // Add Panel
                    } else {
                        $panels[$panel->getId()] = $panel;
                    }
                    break;
                }
            }
            // Search by Bounds
            if ((!empty($criteria['map']['bounds']) || !empty($criteria['search_bounds']))) {
                if ($panels['bounds'] instanceof IDX_Panel) {
                    $panels['bounds']->setDisplay(true);
                } elseif (!empty($panels['bounds'])) {
                    $panels['bounds']['display'] = true;
                } else {
                    $panels = array_merge_recursive(array('bounds' => array('display' => true)), $panels);
                }
            }
            // Search by Polygon
            if (!empty($criteria['map']['polygon'])) {
                if ($panels['polygon'] instanceof IDX_Panel) {
                    $panels['polygon']->setDisplay(true);
                } elseif (!empty($panels['polygon'])) {
                    $panels['polygon']['display'] = true;
                } else {
                    $panels = array_merge_recursive(array('polygon' => array('display' => true)), $panels);
                }
            }
            // Search by Radius
            if (!empty($criteria['map']['radius'])) {
                if ($panels['radius'] instanceof IDX_Panel) {
                    $panels['radius']->setDisplay(true);
                } elseif (!empty($panels['radius'])) {
                    $panels['radius']['display'] = true;
                } else {
                    $panels = array_merge_recursive(array('radius' => array('display' => true)), $panels);
                }
            }
        }
    }

    /**
     * Get available search tags
     * @return IDX_Search_Tag[]
     */
    public static function tags()
    {
        $criteria = array();
        $idx_panels = self::defaults();
        if (!empty($idx_panels) && is_array($idx_panels)) {
            foreach ($idx_panels as $id => $idx_panel) {
                $idx_panel = IDX_Panel::get($id);
                if ($idx_panel instanceof IDX_Panel_Interface_Taggable) {
                    $idx_tags = $idx_panel->getTags();
                    if (empty($idx_tags)) {
                        continue;
                    }
                    if (is_array($idx_tags)) {
                        $criteria = array_merge($criteria, $idx_tags);
                    } else {
                        $criteria[] = $idx_tags;
                    }
                }
            }
        }
        return $criteria;
    }

    /**
     * Default Search Panels
     * @return array
     */
    public static function defaults()
    {

        // Default panels
        $defaults = array(
            'polygon' => array(
                'hidden' => true
            ),
            'radius' => array(
                'hidden' => true
            ),
            'bounds' => array(
                'hidden' => true
            ),
            'location' => array(
                'display' => true
            ),
            'city' => array(
                'display' => true
            ),
            'subdivision' => array(
                'display' => true
            ),
            'zip' => array(
                'display' => true
            ),
            'area' => array(
                'display' => false
            ),
            'county' => array(
                'display' => false
            ),
            'mls' => array(
                'display' => true
            ),
            'address' => array(
                'display' => false
            ),
            'type' => array(
                'display' => true
            ),
            'subtype' => array(
                'display' => true
            ),
            'status' => array(
                'display' => false
            ),
            'price' => array(
                'display' => true
            ),
            'reduced_price' => array(
                'display' => false
            ),
            'rooms' => array(
                'display' => true
            ),
            'bedrooms' => array(
                'display' => false
            ),
            'bathrooms' => array(
                'display' => false
            ),
            'sqft' => array(
                'display' => true
            ),
            'acres' => array(
                'display' => true
            ),
            'year' => array(
                'display' => true
            ),
            'school_elementary' => array(
                'display' => false
            ),
            'school_middle' => array(
                'display' => false
            ),
            'school_high' => array(
                'display' => false
            ),
            'school_district' => array(
                'display' => false
            ),
            'dom' => array(
                'display' => true
            ),
            'dow' => array(
                'display' => true
            ),
            'age' => array(
                'display' => false
            ),
            'waterfront' => array(
                'display' => false
            ),
            'foreclosure' => array(
                'display' => false
            ),
            'shortsales' => array(
                'display' => false
            ),
            'bankowned' => array(
                'display' => false
            ),
            'features' => array(
                'display' => true
            ),
            'office' => array(
                'display' => false
            ),
            'office_id' => array(
                'display' => false
            ),
            'agent' => array(
                'display' => false
            ),
            'agent_id' => array(
                'display' => false
            ),
            'has_open_house' => array(
                'display' => false
            ),
        );

        // Toggle Drive Time Panel Availability
        if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
            $defaults['drive_time'] = [
                'display' => true
            ];
        }

        // Feed specific panels
        $feedClass = IDX_Feed::getClass();
        if (class_exists($feedClass) && method_exists($feedClass, 'getPanels')) {
            return $feedClass::getPanels($defaults);
        }

        $container = Container::getInstance();
        $hooks = $container->get(\REW\Core\Interfaces\HooksInterface::class);
        $defaults = $hooks->hook(Hooks::HOOK_BACKEND_IDX_PANELS)->run($defaults);

        // Return defaults
        return $defaults;
    }

    /**
     * Returns information on a panel that is only relevant to that panel.
     * @return array
     */
    public function typeSpecificJsonSerialize()
    {
        return [];
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array_merge([
            'title' => $this->title,
            'param_name' => $this->inputName,
            'type' => (static::PANEL_TYPE == 'dynamic') ? strtolower($this->fieldType) : static::PANEL_TYPE,
            'hidden' => $this->hidden,
            'collapsed' => $this->closed,
            'display' => $this->display,
            'options' => $this->getOptions(),
            'placeholder' => $this->placeholder,
            'value' => $this->getValue()
        ], $this->typeSpecificJsonSerialize());
    }
}
