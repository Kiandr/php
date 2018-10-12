<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ContainerInterface;

/**
 * "LEC 2015"
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
class Skin_LEC2015 extends Skin_BREW
{

    /**
     * List of features that skin uses (used to define whether feature or feature configuration should display)
     * @var array of strings
     */
    protected static $features = array(
        Skin::INCLUDE_NEWEST_LISTINGS_FIRST,
        Skin::TESTIMONIAL_ASSIGN_AGENT,
        Skin::LINK_COMMUNITY_TO_PAGE,
        Skin::DISPLAY_BACKEND_SETTINGS,
        Skin::HIDE_SEARCH_TAGS,
        Skin::DASHBOARD_SHOW_DISMISSED,
        Skin::COMMUNITY_TAGS,
        Skin::SUBDOMAIN_FEATURE_IMAGE,
    );

    /**
     * Directory
     * @var string
     */
    public static $directory = 'lec-2015';

    /**
     * Name
     * @var string
     */
    protected $name = 'LEC 2015';

    /**
     * Skin Template
     * @var string
     */
    protected $template = '1col';

    /**
     * Key name for skin specific settings
     * @var string
     */
    const SETTINGS_KEY = 'lec.2015.settings';

    /**
     * Index name for navigation cache
     * @var string
     */
    const NAVIGATION_CACHE_INDEX = 'lec.2015.navigation';

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

        // Include skin-specific javascript
        $this->addJavascript('js/skin.js');
        $this->addJavascript('js/idx.js');
    }

    /**
     * @see Skin::getTemplate()
     */
    public function getTemplate(PageInterface $page = null)
    {

        // CMS Homepage
        if (Http_Uri::getUri() === '/') {
            $this->template = 'homepage';

        // Default apps to use 2col
        } else if ($page && in_array($page->info('app'), array('blog', 'directory'))) {
            $this->template = '2col';

        // IDX Page
        } else if ($page && in_array($page->info('app'), array('idx', 'idx-map'))) {
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
        return parent::getTemplate($page);
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

        // Remove body.details from listing 404
        if ($page->info('app') === 'idx' && $page->info('name') === 'details') {
            $listing = $page->info('listing');
            if (empty($listing)) {
                $bodyClass = array_filter($bodyClass, function ($className) {
                    return $className !== 'details';
                });
            }
        }

        // Update <body> className
        $this->page->info('class', implode(' ', $bodyClass));


        // Homepage Only
        if ($page->getTemplate()->getName() === 'homepage') {
            $this->addJavascript('// Picture element HTML5 shiv
                                  document.createElement( "picture" );', 'critical');
            $this->addJavascript('js/lib/picturefill.min.js');
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
            'down_percent'  => 20,
            'interest_rate' => 3.5,
            'mortgage_term' => 30
        );
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'LEC2015\\';
    }
}
