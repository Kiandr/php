<?php

use REW\Core\Interfaces\Page\BackendInterface;

/**
 * IDX Builder
 * @package IDX
 */
class IDX_Builder
{

    /**
     * Map Panels
     * @var boolean
     */
    protected $map = false;

    /**
     * Builder Mode
     * @var string "builder" or "snippet"
     */
    protected $mode = 'builder';

    /**
     * Builder Panels
     * @var array
     */
    protected $panels = array();

    /**
     * Panel List
     * @var array
     */
    protected $list = array();

    /**
     * Split List
     * @var int
     */
    protected $split;

    /**
     * Allow Toggle
     * @var boolean
     */
    protected $toggle;

    /**
     * View Options
     * @var array
     */
    protected $viewOptions = array(
        array('value' => 'grid',        'title' => 'Thumbnails'),
        array('value' => 'detailed',    'title' => 'List')
    );

    /**
     * Sort Options
     * @var array
     */
    protected static $sortOptions = array(
        array('value' => 'DESC-ListingPrice',   'title' => 'Price, Highest First', 'page_title' => 'Highest prices first'),
        array('value' => 'ASC-ListingPrice',    'title' => 'Price, Lowest First', 'page_title' => 'Lowest prices first')
    );

    /**
     * Setup Builder
     * @param array $options
     */
    public function __construct($options = array())
    {

        // Add Map Panels
        if (isset($options['map'])) {
            $this->map = $options['map'];
        }

        // Builder Mode
        if (isset($options['mode'])) {
            $this->mode = $options['mode'];
        }

        // Split Panels
        if (isset($options['split'])) {
            $this->split = $options['split'];
        }

        // Allow Toggle
        if (isset($options['toggle'])) {
            $this->toggle = $options['toggle'];
        }

        // Load IDX Panels
        $this->loadPanels($options['panels']);
    }

    /**
     * Load Search Panels
     * @param array $panels
     * @return void
     */
    public function loadPanels($panels = array())
    {

        // Panel List
        $this->list = array();

        // Build Panels
        $this->panels = array();

        // Default Panels
        $defaults = IDX_Panel::defaults();

        // Search Panels
        $panels = is_array($panels) && isset($panels) ? $panels : $defaults;
        // Do not show drive time panel if drive time is disabled
        if (empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) || !in_array('drivetime', Settings::getInstance()->ADDONS)) {
            unset($panels['drive_time']);
        }
        foreach ($panels as $id => $panel) {
            if (is_array($panel)) {
                $panel = $this->loadPanel($id, $panel);
            }
            if ($panel instanceof IDX_Panel && $panel->isAvailable()) {
                $this->panels[$panel->getId()] = $panel;
            }
        }

        // Default Panels
        foreach ($defaults as $id => $panel) {
            $panel = $this->loadPanel($id, array_merge($panel, array('display' => false)));
            if ($panel instanceof IDX_Panel && $panel->isAvailable()) {
                // Add to List
                $this->list[$panel->getId()] = array('title' => $panel->getTitle());
                // Add to Panels
                if (!isset($this->panels[$panel->getId()])) {
                    $this->panels[$panel->getId()] = $panel;
                }
            }
        }

        // Remove Map Panels
        if (empty($this->map) || empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
            unset($this->panels['bounds']);
            unset($this->panels['radius']);
            unset($this->panels['polygon']);
        }
    }

    /**
     * Load Search Panel
     * @param string $id
     * @param array $options
     * @return IDX_Panel|null
     */
    public function loadPanel($id, $options = array())
    {
        return IDX_Panel::get($id, array(
            'mode'      => $this->mode,
            'display'   => !empty($options['display'])   ? true : false,
            'closed'    => !empty($options['collapsed']) ? true : false,
            'hidden'    => !empty($options['hidden'])    ? true : false
        ));
    }

    /**
     * Get Panels
     * @return array
     */
    public function getPanels()
    {
        return $this->panels;
    }

    /**
     * Get Panel List
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Get View Options
     * @return array
     */
    public function getViewOptions()
    {
        $viewOptions = Hooks::hook(Hooks::HOOK_IDX_BUILDER_VIEW_OPTIONS)->run($this->viewOptions);
        if (is_array($viewOptions)) {
            return $viewOptions;
        }
        return $this->viewOptions;
    }

    /**
     * Get Sort Options. If all is set to true, this will add all newest first variants. This is so
     * that if we were using DOW but all of a sudden can use DOM, the title can still be retrieved
     * for the old one.
     * @param bool $all
     * @return array
     */
    public static function getSortOptions($all = false)
    {
        $sortOptions = static::$sortOptions;
        // Sort by ListingDOM, ListingDOW, timestamp_created
        if (Skin::hasFeature(Skin::INCLUDE_NEWEST_LISTINGS_FIRST)) {
            $title = 'New Listings First';
            $page_title = 'New listings first';

            if ($all || IDX_Panel::checkField('ListingDOM')) {
                $sortOptions[] = array('value' => 'ASC-ListingDOM', 'title' => $title, 'page_title' => $page_title);
                if (!$all) {
                    $all = null;
                }
            }

            if ($all || ($all !== null && IDX_Panel::checkField('ListingDOW'))) {
                $sortOptions[] = array('value' => 'ASC-ListingDOW', 'title' => $title, 'page_title' => $page_title);
                if (!$all) {
                    $all = null;
                }
            }

            if ($all || ($all !== null && IDX_Panel::checkField('timestamp_created'))) {
                $sortOptions[] = array('value' => 'DESC-timestamp_created', 'title' => $title, 'page_title' => $page_title);
                if (!$all) {
                    $all = null;
                }
            }
        }
        return $sortOptions;
    }

    /**
     * Get Split Count
     * @return int
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * Set Split Count
     * @param int $split
     * @return void
     */
    public function setSplit($split)
    {
        $this->split = $split;
    }

    /**
     * Set if allowed to toggle collapsed
     * @param boolean $toggle
     * @return void
     */
    public function setToggle($toggle)
    {
        $this->toggle = $toggle;
    }

    /**
     * Display Builder Panels
     * @param BackendInterface $page
     * @return void
     */
    public function display(BackendInterface &$page)
    {

        // Add IDX Builder Javascript
        $this->addJavascript($page);

        // Add Missing IDX Panels
        IDX_Panel::displayMissing($this->panels, $_REQUEST);

        // Do not split
        if (is_null($this->split)) {
            $list = array($this->panels);
        } else {
            // Split Panels
            $list = array(
                array_slice($this->panels, 0, $this->split),
                array_slice($this->panels, $this->split)
            );
        }

        // Display Panels
        foreach ($list as $i => $panels) {
            // Advanced Panels
            $advanced = ($i === 1);
            if (!empty($advanced)) {
                echo '<div class="idx-panels advanced" style="background: #F1F5F8; padding: 10px;">';
                echo '<h2><span class="ui-icon ui-icon-minusthick" style="float: left; margin: 2px 3px 0 0;"></span> More Search Options</h2>';
                echo '<div class="advanced-panels">';
            } else {
                echo '<div class="idx-panels">';
            }

            // Display Panels
            foreach ($panels as $panel) {
                if (isset($this->toggle)) {
                    $panel->setToggle($this->toggle);
                }
                $panel->display();
            }

            // Advanced Panels
            if (!empty($advanced)) {
                echo '</div>';
                echo '</div>';
            } else {
                echo '</div>';
            }
        }
    }

    /**
     * Add IDX Builder Javascript to Page Instance (This has to be ran to get required functionality)
     * @param BackendInterface $page
     * @return void
     */
    public function addJavascript(BackendInterface &$page)
    {

        // Define required `window.GOOGLE_API_KEY` variable
        if ($apiKey = \Settings::get('google.maps.api_key')) {
            $page->addJavascript(sprintf(
                'var GOOGLE_API_KEY = %s;',
                json_encode($apiKey)
            ), 'global');
        }

        // Display IDX builder map
        if (!empty($this->map)) {
            $settings = \Settings::getInstance();

            // Radiuses
            $radiuses = [];
            if (!empty($_REQUEST['map']['radius']) && is_string($_REQUEST['map']['radius'])) {
                $radiuses = json_decode($_REQUEST['map']['radius'], true);
            }

            // Polygons
            $polygons = [];
            if (!empty($_REQUEST['map']['polygon']) && is_string($_REQUEST['map']['polygon'])) {
                $polygons = json_decode($_REQUEST['map']['polygon'], true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    $polygons = [$_REQUEST['map']['polygon']];
                }
            }

            // Define `IDX_BUILDER_MAP`
            $page->addJavascript(sprintf(
                'var IDX_BUILDER_MAP = %s;',
                json_encode([
                    'zoom' => !empty($_REQUEST['map']['zoom']) ? intval($_REQUEST['map']['zoom']) : intval($settings->SETTINGS['map_zoom']),
                    'center' => [
                        'lat' => !empty($_REQUEST['map']['latitude'])  ? floatval($_REQUEST['map']['latitude'])  : floatval($settings->SETTINGS['map_latitude']),
                        'lng' => !empty($_REQUEST['map']['longitude']) ? floatval($_REQUEST['map']['longitude']) : floatval($settings->SETTINGS['map_longitude'])
                    ],
                    'polygons' => array_map(function ($polygon) {
                        return array_map(function ($point) {
                            list ($lat, $lng) = explode(' ', $point);
                            return ['lat' => floatval($lat), 'lng' => floatval($lng)];
                        }, explode(',', $polygon));
                    }, $polygons),
                    'radiuses' => array_map(function ($radius) {
                        list ($lat, $lng, $radius) = explode(',', $radius);
                        return ['radius' => $radius, 'lat' => floatval($lat), 'lng' => floatval($lng)];
                    }, $radiuses)
                ])
            ), 'global');
        }
    }
}
