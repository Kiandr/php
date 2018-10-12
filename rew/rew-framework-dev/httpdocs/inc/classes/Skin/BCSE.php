<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ContainerInterface;

/**
 * "Barbara Corcoran Special Edition"
 *
 * Order of stylesheets:
 *  css/config.less
 *  css/schemes/:scheme/config.less
 *  css/skin.less
 *  css/compliance.less
 *  tpl/tpl/pages/:tpl/page.less
 *  css/schemes/:scheme/scheme.less
 *  css/custom.less
 *
 * @package REW
 * @subpackage Skin
 */
class Skin_BCSE extends Skin_BREW
{

    /**
     * List of features that skin uses (used to define whether feature or feature configuration should display)
     * @var array of strings
     */
    protected static $features = array(
        Skin::DISPLAY_BACKEND_SETTINGS,
        Skin::INSTALL_GUARANTEED_SNIPPET,
        Skin::INSTALL_RADIO_LANDING_PAGE,
        Skin::HIDE_SEARCH_TAGS,
        Skin::LINK_COMMUNITY_TO_PAGE,
        Skin::ENABLE_RADIO_AD_IMAGES,
        Skin::INCLUDE_NEWEST_LISTINGS_FIRST,
        Skin::AGENT_SPOTLIGHT,
        Skin::SAVED_SEARCHES_RESPONSIVE,
        Skin::AGENT_EDIT_NETWORKS,
        Skin::SUBDOMAIN_FEATURE_IMAGE,
    );

    /**
     * Directory
     * @var string
     */
    public static $directory = 'bcse';

    /**
     * Name
     * @var string
     */
    protected $name = 'Barbara Corcoran Special Edition';

    /**
     * Skin Template
     * @var string
     */
    protected $template = '1col';

    /**
     * Skin Settings
     * @var array
     */
    private $_settings;

    /**
     * Skin Settings Key
     * @var string
     */
    const SETTINGS_KEY = 'bcse.settings';

    /**
     * Create Skin
     * @param string $scheme
     * @param ContainerInterface $container
     * @uses Resource::__construct
     */
    public function __construct($scheme = 'default', ContainerInterface $container = null)
    {
        if ($container === null) {
            $container = Container::getInstance();
        }

        // Call BREW Construct
        $container->callConstructor($this, parent::class, ['directory' => self::$directory, 'scheme' => $scheme]);

        // Include Skin's Config
        $this->addStylesheet('css/config.less');

        // Include Scheme's Config
        $this->addStylesheet('schemes/' . $scheme . '/config.less');

        // Include Skin's Stylesheet
        $this->addStylesheet('css/skin.less');
        $this->addStylesheet('css/compliance.less');

        // Only Include window.less If Not Popup Window (IE9 Issue w/ Media Queries)
        if (!isset($_GET['popup'])) {
            $this->addStylesheet('css/window.less');
        }

        // Include Skin's Javascript
        $this->addJavascript('js/skin.js');
    }

    /**
     * @see Skin::getTemplate()
     */
    public function getTemplate(PageInterface $page = null)
    {
        $page = $page ?: $this->getPage();

        // CMS Homepage
        if (Http_Uri::getUri() === '/' && Settings::getInstance()->SETTINGS['agent'] === 1) {
            $this->template = 'homepage';

        // Default apps to use 2col
        } else if (in_array($page->info('app'), array('blog', 'directory'))) {
            $this->template = '2col';

        // IDX Page
        } else if (in_array($page->info('app'), array('idx', 'idx-map'))) {
            $this->template = '1col';

            // IDX Details Page
            if (in_array($page->info('name'), array('details', 'map', 'birdseye', 'streetview', 'local'))) {
                $this->template = 'idx/detail';

            // IDX Search Results
            } elseif (in_array($page->info('name'), array('search', 'search_map'))) {
                $this->template = 'idx/search';
            }
        }

        // Load Page Template
        return parent::getTemplate();
    }

    /**
     * Load Extra Sources
     * @return void
     */
    protected function extraSources()
    {

        // Run parent
        parent::extraSources();

        // Include scheme styles
        $scheme = $this->getScheme();
        $this->addStylesheet('schemes/' . $scheme . '/scheme.less');

        // Include custom styles
        $this->addStylesheet('css/custom.less');

        // Page instance
        $page = $this->getPage();

        // <body> class list
        $bodyClass = $this->page->info('class');
        $bodyClass = !empty($bodyClass) ? explode(' ', $bodyClass) : array();

        // Add body.idx-snippet
        if (!empty($_REQUEST['snippet'])) {
            $bodyClass[] = 'idx-snippet';
        }

        // Add body.has-slideshow
        if ($page->moduleLoaded('slideshow')) {
            $bodyClass[] = 'has-slideshow';
        }

        // Remove body.details from listing 404
        if ($page->info('app') === 'idx' && $page->info('name') === 'details') {
            $listing = $page->info('listing');
            if (empty($listing)) {
                $bodyClass = array_filter($bodyClass, function ($className) {
                    return $className !== 'details';
                });
            }
        }

        // Homepage feature search
        if ($template = $page->getTemplate()) {
            $feature = $page->variable('feature');
            if ($template->getName() === 'homepage' && in_array($feature, array('agent', 'search', 'photo'))) {
                $bodyClass[] = $feature . '-feature';
            }
        }

        // Update <body> className
        $this->page->info('class', implode(' ', $bodyClass));

        // Quick search bar
        if ((empty($_REQUEST['snippet'])
            || $page->moduleLoaded('agents')
            || $page->moduleLoaded('property-valuation')
            || $page->moduleLoaded('idx-featured-search')
            || in_array('search-feature', $bodyClass)
            || ($template->getName() === 'homepage'))
            && ($template->getName() != 'rate') && ($page->info('name') != 'details')
        ) {
            $search = ($page->info('app') === 'idx' && $page->info('name') === 'search');
            $search_map = ($page->info('app') === 'idx-map' && $page->info('name') === 'search_map');
            if ($page->info('app') !== 'bdx' && $page->info('app') !== 'rt') {
                $page->container('sub-feature')->addModule('idx-search', array(
                    'button'    => $search ? 'Refine' : $search_map ? 'Update' : null,
                    'advanced'  => $search || $search_map,
                    'prepend'   => ($template->getName() === 'rate')
                ));
            }
        }
    }

    /**
     * Return skin's settings
     * @return array
     */
    public static function getSettings()
    {
        $settings = Settings::getInstance()->get(self::SETTINGS_KEY);
        $settings = json_decode($settings, true);
        return is_array($settings) ? $settings : array(
            'agent_id'      => 'RAND',
            'agent_phone'   => true,
            'agent_cell'    => true
        );
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'BCSE\\';
    }
}
