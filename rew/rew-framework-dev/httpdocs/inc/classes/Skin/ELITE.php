<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Page\TemplateInterface;

/**
 * "Elite"
 *
 * @package REW
 * @subpackage Skin
 */
class Skin_ELITE extends Skin
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * Groups for all IDX panels to be put into
     */
    const GROUP_PROPERTY_INFO = 0;
    const GROUP_PROPERTY_SIZE = 1;
    const GROUP_FEATURES = 2;
    const GROUP_STATUS = 3;

    /**
     * Features this skin supports
     * @var array
     */
    protected static $features = array(
        Skin::REGISTRATION_ON_MORE_PICS,
        Skin::AGENT_SPOTLIGHT,
        Skin::COMMUNITY_VIDEO_LINKS,
        Skin::INLINE_POPUPS,
        Skin::DISPLAY_BACKEND_SETTINGS,
        Skin::INCLUDE_NEWEST_LISTINGS_FIRST,
        Skin::AGENT_EDIT_NETWORKS,
        Skin::INSTALL_GUARANTEED_SNIPPET,
        Skin::INSTALL_RADIO_LANDING_PAGE,
        Skin::HIDE_SEARCH_TAGS,
        Skin::INCLUDE_TESTIMONIAL_LINK,
        Skin::LINK_COMMUNITY_TO_PAGE,
        Skin::ENABLE_RADIO_AD_IMAGES,
        Skin::PROVIDES_SEARCH_RESULTS_JS,
        Skin::PROVIDES_SEARCH_MAP_JS,
        Skin::PRE_BUILT_ASSETS,
        Skin::DASHBOARD_SHOW_DISMISSED,
        Skin::SUBDOMAIN_FEATURE_IMAGE,
    );

    /**
     * Snippets to propagate to new subdomains
     * @var array
     */
    protected $subdomainSnippets = array(
        'navigation',
        'mobile-navigation',
        'popular-pages',
        'useful-links',
        'phone-number',
        'offices',
        'footer-quote',
        'company-info',
    );

    /**
     * Directory
     * @var string
     */
    public static $directory = 'elite';

    /**
     * Quick Search - Mirrored Fields
     */
    protected $qs_mirrored_fields = array(
        'search_location',
        'minimum_price',
        'maximum_price',
        'minimum_rent',
        'maximum_rent',
        'map[polygon]',
        'map[radius]'
    );

    /**
     * Name
     * @var string
     */
    protected $name = 'Elite';

    /**
     * Skin Template
     * @var string
     */
    protected $template = '1col';

    /**
     * Configured breakpoints
     * @var array|null
     */
    protected $_breakpoints;

    /**
     * Production or development assets
     * @var string
     */
    protected $_asset_type;

    /**
     * Skin Settings Key
     * @var string
     */
    const SETTINGS_KEY = 'elite.settings';

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

        // Call parent Construct
        $container->callConstructor($this, parent::class, ['directory' => self::$directory, 'scheme' => $scheme]);

        Profile::setDefaultReportType(Profile::REPORT_TYPE_UIKIT);
        $this->config('dashboard.results.limit', 8);
    }

    /**
     * Get fields that need to be mirrored between the quicksearch and advanced quicksearch
     * @return array
     */
    public function getQsMirroredFields()
    {
         $qs_fields = $this->qs_mirrored_fields;
        foreach (['saved_search_id', 'edit_search', 'lead_id', 'create_search'] as $ss_request) {
            if (array_key_exists($ss_request, $_REQUEST)) {
                $qs_fields[] = $ss_request;
            }
        }
        return $qs_fields;
    }

    /**
     * Gets photo sizes appropriate for our breakpoints. NOTE that this will not work with urls, so
     * don't do it, or update it to support them.
     * @param string $src
     * @return array
     */
    public function getPhotoSizes($src)
    {
        if (empty($this->_breakpoints)) {
            // Load breakpoints once.
            $this->_breakpoints = json_decode(file_get_contents($this->getPath() . '/config/breakpoints.json'), true);
        }

        $aspect = 1;
        if (preg_match('#^/thumbs/([0-9]+)x?([0-9]+)?/(f/)?#', $src, $match)) {
            $width = $match[1];
            $height = $match[2];
            $src = substr($src, strlen($match[0]));
            $aspect = $height ? $width / $height : $aspect;
        }

        $sizes = array();
        $maxWidth = 0;
        $maxName = null;
        foreach ($this->_breakpoints as $name => $min_width) {
            $width = $min_width;
            $height = $width * $aspect;
            $sizes[$name] = '/thumbs/' . $width . 'x' . $height . '/' . $src;
            if ($width > $maxWidth) {
                $maxName = $name;
                $maxWidth = $width;
            }
        }
        if ($maxWidth) {
            // The largest size should not use the thumbnailer
            $sizes[$maxName] = ($src[0] != '/' ? '/' : '') . $src;
        }

        return $sizes;
    }

    /**
     * @return string
     */
    protected function getAssetType()
    {
        if (empty($this->_asset_type)) {
            $this->_asset_type = in_array('dev', Http_Host::getParts()) ? 'dev' : 'min';
        }
        return $this->_asset_type;
    }

    /**
     * @inheritDoc
     */
    public function bodyClass()
    {

        // Page Instance
        $page = $this->getPage();

        // Page Classes
        $classes = array();

        // Page Information
        $classes[] = $this->settings['SKIN'];
        $classes[] = $this->settings['SKIN_SCHEME'];
        $classes[] = $this->settings['IDX_FEED'];

        // Sticky-header class @TODO add backend support to turn on/off
        $classes[] = 'sticky-header';

        // Agent Sub-Domain
        if ($this->settings['SETTINGS']['agent'] != 1) {
            $classes[] = 'agent-site';
        }

        $classes[] = ($page->info('app') === 'cms' ? 'pg-' : '') . $page->info('name');

        // Current Classes
        $classes[] = $page->info('class');

        // Page Template
        if ($page->info('app') === 'cms') {
            $template = $page->getTemplate();
            if ($template instanceof TemplateInterface) {
                $classes[] = 'tpl-' . $template->getName();
            }
        }

        // Set Body Class
        $page->info('class', implode(' ', array_filter($classes)));
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(PageInterface $page = null)
    {

        // CMS Homepage
        if (Http_Uri::getUri() === '/' && $this->settings['SETTINGS']['agent'] === 1) {
            $this->template = 'homepage';

            // Default apps to use 2col
        } else if ($page && in_array($page->info('app'), array('blog'))) {
            $this->template = '2col';
        } else if ($page && in_array($page->info('app'), array('directory'))) {
            $this->template = '1col';

            // IDX Page
        } else if ($page && in_array($page->info('app'), array('idx', 'idx-map'))) {
            $this->template = '1col';

            // IDX Details Page
            if (in_array($page->info('name'), array('details', 'map', 'birdseye', 'streetview', 'local'))) {
                $this->template = 'idx/detail';

                // IDX Search Results
            } elseif (in_array($page->info('name'), array('search'))) {
                $this->template = 'idx/search';
            } elseif (in_array($page->info('name'), array('search_map'))) {
                $this->template = 'idx/search_map';
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

        // Generate Body Class
        $this->bodyClass();

        $this->addStylesheet(static::getBuildUrlForFile(sprintf('css/app.%s.css', $this->getAssetType())), 'static', false);
        $this->addJavascript(static::getBuildUrlForFile(sprintf('js/app.%s.js', $this->getAssetType())), 'static', false, !in_array($this->page->info('app'), ['bdx','directory']));

        if ($this->page->info('app') === 'bdx') {
            $this->addJavascript(static::getBuildUrlForFile(sprintf('js/app_builder.%s.js', $this->getAssetType())), 'static', false);
            $this->addStylesheet(static::getBuildUrlForFile(sprintf('css/app_builder.%s.css', $this->getAssetType())), 'static', false);
        }

        if ($this->page->info('app') === 'directory') {
            $this->addJavascript(static::getBuildUrlForFile(sprintf('js/app_directory.%s.js', $this->getAssetType())), 'static', false);
            $this->addStylesheet(static::getBuildUrlForFile(sprintf('css/app_directory.%s.css', $this->getAssetType())), 'static', false);
        }

        // Run parent
        parent::extraSources();

        // Page instance
        $page = $this->getPage();

        // <body> class list
        $bodyClass = $this->page->info('class');
        $bodyClass = !empty($bodyClass) ? explode(' ', $bodyClass) : array();

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
            if ($feature = $page->variable('feature')) {
                $bodyClass[] = Format::slugify($feature) . '-feature';
            }
        }

        // Update <body> className
        $this->page->info('class', implode(' ', $bodyClass));

        // Quick search bar
        if (!isset($_GET['popup'])) {
            $search = ($page->info('app') === 'idx' && $page->info('name') === 'search');
            $search_map = ($page->info('app') === 'idx-map' && $page->info('name') === 'search_map');
            if ($page->info('app') !== 'bdx' && $page->info('app') !== 'rt') {
                // Search
                $page->container('quicksearch')->addModule('idx-search', array(
                    'button'    => $search ? 'Search' : ($search_map ? 'Update' : null),
                    'advanced'  => false,
                    'searchbar' => true,
                    'form-open' => false,
                    'form-close' => false,
                    'has-results' => $search || $search_map,
                    'prepend'   => ($template->getName() === 'rate'),
                    'hideTags' => true
                ));

                // Advanced search
                $page->container('quicksearch-advanced')->addModule('idx-search', array(
                    'template' => 'advanced.tpl.php',
                    'button'    => $search ? 'Refine' : $search_map ? 'Update' : null,
                    'advanced'  => true,
                    'searchbar' => false,
                    'form-open' => false,
                    'form-close' => false,
                    'has-results' => $search || $search_map,
                    'prepend'   => ($template->getName() === 'rate'),
                    'hideTags' => true
                ));
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function addForcedVerification()
    {
        $this->addJavascript(
            "REW.registration = {type: 'forcedVerification', disallowClose: true};",
            'dynamic',
            false
        );
    }

    /**
     * @inheritDoc
     */
    protected function addForcedRegistration(array $listing, $registration_required)
    {
        $this->addJavascript("REW.registration = {type: 'forcedRegistration', disallowClose: " . ($registration_required ? 'true' : 'false') . '};'
            . "REW.settings.ajax.urls.register = " . json_encode($listing['url_register'] . '?popup'), 'dynamic', false);
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
     * Returns type to use for profiler
     * @return string
     */
    public function getProfileType()
    {
        return 'UIKit_REW';
    }

    /**
     * @inheritDoc
     */
    public function loadMapApi()
    {
    }

    /**
     * Display Skin
     * @param bool $return Return Output
     * @return string|null
     */
    public function display($return = false)
    {

        $backendUser = Auth::get();
        $output = '';
        $page = $this->container->get(PageInterface::class);

        // Check if we should minify/cache. Don't minify if not caching as it slows down the response
        // slightly.
        if (isset($_REQUEST['uncache']) || (
                // Logged in
                (!$this->user->isValid()) && (!$backendUser || !$backendUser->isValid())
                // Request arguments
                && empty($_SERVER['QUERY_STRING'])
                // Not posting
                && $_SERVER['REQUEST_METHOD'] != 'POST'
                // Only the main site
                && !Http_Host::getSubdomain()
                // Don't cache for rew, at least not on the dev site
                && (!Settings::isREW() || !in_array('dev', Http_Host::getParts())))
                // Don't cache if caching is disabled
                && $this->page->variable('enablePageCache')
        ) {
            // Check cache
            $fileName = Format::slugify(str_replace('/', '-', Http_Uri::getUri())) ?: 'homepage';

            $cache = new Cache(array(
                'name' => 'html/' . $fileName . '.html',
                'expires' => (60 * 60 * 12) // Expires in 12 Hours
            ));

            if (isset($_REQUEST['uncache'])) {
                @unlink($cache->getPath() . '/' . $cache->getName());
                header('X-APP-Cache: RESET');
            } else if (!($output = $cache->get())) {
                header('X-APP-Cache: MISS');
                $output = parent::display(true);

                // Minify HTML
                $output = Minify_HTML::minify($output);

                $cache->save($cache->getName(), $output);
            } else {
                header('X-APP-Cache: HIT');
            }
        }

        if (empty($output)) {
            $output = parent::display(true);
        }

        if ($return) {
            return $output;
        }
        echo $output;
    }

    /**
     * Builds an attribute string for the specified activeView
     *
     * @param string $activeView
     * @param array $viewOptions
     * @return string
     */
    public static function buildSearchAttributesForView($activeView, array $viewOptions)
    {
        $attributes = array();
        if (!$activeView || is_array($activeView) || is_object($activeView) || !array_key_exists($activeView, $viewOptions)) {
            reset($viewOptions);
            $activeView = key($viewOptions);
        }

        foreach ($viewOptions as $view => $classes) {
            $attributes[] = 'data-view-' . $view . '="' . Format::htmlspecialchars($classes) . '"';
            if ($view == $activeView) {
                $attributes[] = 'class="' . Format::htmlspecialchars($classes) . '"';
            }
        }

        return ' ' . implode(' ', $attributes);
    }

    public function getSearchDisplayFields()
    {
        return array(
            'ListingPrice', 'ListingImage', 'ListingMLS', 'Address', 'AddressCity', 'NumberOfBedrooms',
            'NumberOfBathrooms', 'NumberOfSqFt', 'ListingRemarks', 'ListingRemarks', 'ListingMLSNumber',
        );
    }

    /**
     * Gets the file for the backend WYSIWYG editor to consume. Nom nom.
     * @return string
     */
    public static function getWYSIWYGHelperCSSFile()
    {

        return static::getBuildUrlForFile('css/app.min.css');
    }

    /**
     * Gets the build url for a file
     * @param string $file
     * @return string
     */
    public function getBuildUrlForFile($file)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(SkinInterface::class, __FUNCTION__, func_get_args());
        }

        $buildPath = $this->settings['DIRS']['SKINS'] . str_replace('skin_', '', strtolower(__CLASS__)) . '/build/';

        if (file_exists($absPath = $buildPath . $file)) {
            $absPath .= '?' . filemtime($absPath);
        }

        return '//' . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $absPath);
    }

    /**
     * Renders any critical stylesheets
     */
    public function renderCriticalStylesheets()
    {

        $criticalPath = sprintf('%s/build/css/critical.%s.css', $this->getPath(), $this->getAssetType());
        if (file_exists($criticalPath)) {
            echo sprintf('<style id="critical-css">%s</style>', file_get_contents($criticalPath));
        }
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'Elite\\';
    }
}
