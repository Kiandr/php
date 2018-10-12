<?php

use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\ResourceInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\InstallerInterface;
use REW\Core\Interfaces\User\SessionInterface;
use REW\Core\Interfaces\Page\TemplateInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\SnippetFactoryInterface;
use REW\Providers\SkinProvider;

/**
 * Skin extends Resource and is used for loading skins and schemes to display modules
 * @package REW
 * @subpackage Resource
 */
abstract class Skin extends Resource implements SkinInterface
{
    /**
     * The default module path (used in addition to Skin::getAbsolutePath)
     * @const
     */
    const DEFAULT_MODULE_PATH = __DIR__ . '/../modules';

    /**
     * No character limit restrictions for featured communities module
     */
    const COMMUNITY_TAGS = 'community.tags';

    /**
     * No character limit restrictions for featured communities module
     */
    const COMMUNITY_DESCRIPTION_NO_LIMIT = 'community.description.no.limit';

    /**
     * Disable the "anchor links" section for featured communities module
     */
    const COMMUNITY_DISABLE_ANCHOR_LINKS = 'community.disable.anchor.links';

    /**
     * Does this skin support the more pics requiring registration feature?
     */
    const REGISTRATION_ON_MORE_PICS = 'registration_on_more_pics';

    /**
     * We should display Website Settings in the backend?
     */
    const DISPLAY_BACKEND_SETTINGS = 'display.backend.settings';

    /**
     * Should we disable the Search Cities IDX field in the backend?
     */
    const DISABLE_SEARCH_CITIES = 'disable.search.cities';

    /**
     * We should include "Newest First" sort option
     */
    const INCLUDE_NEWEST_LISTINGS_FIRST = 'include.newest.listings.first.option';

    /**
     * We should install the guaranteed sold snippet
     */
    const INSTALL_GUARANTEED_SNIPPET = 'install.guaranteed.snippet';

    /**
     * We should install the radio landing page snippet
     */
    const INSTALL_RADIO_LANDING_PAGE = 'install.radio.landing.page';

    /**
     * We should hide search tags in the search module
     */
    const HIDE_SEARCH_TAGS = 'hide.search.tags';

    /**
     * We should link communities to pages
     */
    const LINK_COMMUNITY_TO_PAGE = 'link.community.to.page';

    /**
     * We should link communities to pages
     */
    const ENABLE_RADIO_AD_IMAGES = 'enable.radio.ad.images';

    /**
     * We should enable the agent spotlight on details pages (and the backend settings of course)
     */
    const AGENT_SPOTLIGHT = 'agent.spotlight';

    /**
     * We should display all the social media networks on the agent edit page
     */
    const AGENT_EDIT_NETWORKS = 'agent.edit.social.networks';

    /**
     * We should include a link field when editing/adding testimonials
     */
    const INCLUDE_TESTIMONIAL_LINK = 'include.testimonial.link';

    /**
     * We should be able to assign agents to client testimonials
     */
    const TESTIMONIAL_ASSIGN_AGENT = 'testimonial.assign.agent';

    /**
     * We should provide an interface to add links to videos in the featured communities module
     */
    const COMMUNITY_VIDEO_LINKS = 'community.video.links';

    /**
     * We use inline popups (as opposed to iframes)
     */
    const INLINE_POPUPS = 'inline_popups';

    /**
     * We provide the search results js
     */
    const PROVIDES_SEARCH_RESULTS_JS = 'provides.search.results.js';

    /**
     * We provide the search map js
     */
    const PROVIDES_SEARCH_MAP_JS = 'provides.search.map.js';

    /**
     * We provide pre-built assets
     */
    const PRE_BUILT_ASSETS = 'pre.built.assets';

    /**
     * Show dismissed listings in the dashboard
     */
    const DASHBOARD_SHOW_DISMISSED = 'dashboard.show.dismissed';

    /**
     * Show open more options toggle on website settings page.  When enabled,
     * the skin is able to define a section of IDX search criteria that is only
     * visible when a "More Search Options" link is clicked
     */
    const MORE_SEARCH_OPTIONS = 'more.search.options';

    /**
     * Show developments module if skin supports it.
     */
    const REW_DEVELOPMENTS = 'rew.developments.module';

    /**
     * Show feature image uploader on subdomain homepage editor
     */
    const SUBDOMAIN_FEATURE_IMAGE = 'subdomain.feature.image';

    /**
     * Show saved searches responsive template toggle on settings idx page
     */
    const SAVED_SEARCHES_RESPONSIVE = 'saved.searches.responsive';

    /**
     * Directory
     * @var string
     */
    public static $directory = 'default';

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * Name
     * @var string
     */
    protected $name;

    /**
     * Scheme Name
     * @var string
     */
    protected $scheme;

    /**
     * Controller File
     * @var string
     */
    protected $controller = 'skin.php';

    /**
     * Skin Template
     * @var string
     */
    protected $template = 'skin.tpl';

    /**
     * Page Templates
     * @var array
     */
    protected $templates;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var SessionInterface
     */
    protected $user;

    /**
     * @var IDXInterface
     */
    protected $idx;

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var SnippetFactoryInterface
     */
    protected $snippet;

    /**
     * @var InstallerInterface
     */
    protected $installer;

    /**
     * @var string
     */
    protected $favicon;

    /**
     * List of features that skin uses (used to define whether feature or feature configuration should display)
     * @var array of strings
     */
    protected static $features = array();

    /**
     * Default Configuration
     */
    protected $config = [
        'dashboard.results.limit' => 9
    ];

    /**
     * Create Skin
     * @param string $directory
     * @param string $scheme
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     * @param SessionInterface $user
     * @param IDXInterface $idx
     * @param DBFactoryInterface $dbFactory
     * @param LogInterface $log
     * @param SnippetFactoryInterface $snippet
     * @param InstallerInterface $installer
     * @uses Resource::__construct
     */
    public function __construct(
        $directory,
        $scheme = 'default',
        ContainerInterface $container = null,
        SettingsInterface $settings = null,
        SessionInterface $user = null,
        IDXInterface $idx = null,
        DBFactoryInterface $dbFactory = null,
        LogInterface $log = null,
        SnippetFactoryInterface $snippet = null,
        InstallerInterface $installer = null
    ) {
        if ($container === null) {
            $container = Container::getInstance();
        }
        if ($settings === null) {
            $settings = $container->get(SettingsInterface::class);
        }
        if ($user === null) {
            $user = $container->get(SessionInterface::class);
        }
        if ($idx === null) {
            $idx = $container->get(IDXInterface::class);
        }
        if ($dbFactory === null) {
            $dbFactory = $container->get(DBFactoryInterface::class);
        }
        if ($log === null) {
            $log = $container->get(LogInterface::class);
        }
        if ($snippet === null) {
            $snippet = $container->get(SnippetFactoryInterface::class);
        }
        if ($installer === null) {
            $installer = $container->get(InstallerInterface::class);
        }

        $container->callConstructor(
            $this,
            parent::class,
            ['path' => $directory ? $this->getAbsolutePath($directory) : null]
        );

        $this->container = $container;
        $this->settings = $settings;
        $this->user = $user;
        $this->idx = $idx;
        $this->dbFactory = $dbFactory;
        $this->log = $log;
        $this->snippet = $snippet;
        $this->installer = $installer;
        $this->setScheme($scheme);

        // Default is in both inc/skins/default/modules/.. and inc/modules/...
        Module::addPathForNamespace(self::getModuleNamespace(), static::DEFAULT_MODULE_PATH);

        foreach (self::getModuleNamespaces() as $skinPath => $namespace) {
            Module::addPathForNamespace($namespace, $skinPath . '/modules/');
        }
    }

    /**
     * Display Skin
     * @param bool $return Return Output
     * @return string
     */
    public function display($return = false)
    {
        // Set Page
        $this->setPage($this->container->get(PageInterface::class));
        // Display Skin
        return parent::display($return);
    }

    /**
     * Build Sources
     * @see Resource::buildSources()
     */
    public function buildSources()
    {
        // Add Locale JS
        $this->addJavascript(Locale::toJS(), 'static');
        // Load Page Modules
        $this->page->loadModules();
        // Build Template
        $template = $this->page->getTemplate();
        if ($template instanceof ResourceInterface) {
            $template->buildSources();
        }
        // Load Extra Sources
        $this->extraSources();
        // Build Page Sources
        $this->page->buildSources();
        // Build Skin Sources
        parent::buildsources();
        // Merge Skin & Page Sources
        $this->sources = array_merge($this->getSources(), $this->page->getSources());
    }

    /**
     * Load Extra Skin Sources
     * @return void
     */
    protected function extraSources()
    {
        // Registration settings
        $registration = $this->settings['SETTINGS']['registration'];
        $registration_verify = !empty($this->settings['SETTINGS']['registration_verify']);
        $registration_required = ($this->settings['SETTINGS']['registration_required'] === true);

        // Force Registration After # Of Views
        if (is_numeric($registration) && !empty($registration)) {
            $registration = ($this->user->getViews() > $registration) ? true : false;
        }

        // Logged into backend
        $backend_user = Auth::get();

        // Ignore if popup window or CMS listings
        if ((!isset($_GET['popup']) && $this->idx->getLink() != 'cms')) {
            // Display compliance Terms of Service as pop up if required
            global $_COMPLIANCE;
            if (!empty($_COMPLIANCE['terms_required']) && !empty($_COMPLIANCE['pages']) && ($_SESSION['compliance_tos'] != true) && !empty($backend_user) && !$backend_user->isValid()) {
                $this->openTos();

            // Require email verification
            } elseif ($this->user->isValid() && ($this->user->info('verified') != 'yes') // Is logged in and not already verified
                && ($_GET['load_page'] != 'verify' && $_GET['id'] != 'unsubscribe' && $_GET['id'] != 'contact') // Not on verify/unsubscribe page
                && (($registration_verify && !Validate::verifyWhitelisted($this->user->info('email'))) || Validate::verifyRequired($this->user->info('email')))
            ) {
                $this->addForcedVerification();

            // These pages require registration...
            } elseif (in_array($_GET['load_page'], array('details', 'map', 'streetview', 'birdseye', 'local'))
            ) {
                // Require lead registration
                if (!$this->user->isValid() && !empty($registration)) {
                    if (!empty($backend_user) && $backend_user->isValid()) {
                        // Do nothing...
                    } else {
                        // Show form
                        global $listing;

                        // Get the bdx property
                        $property = $this->page->info('bdx.property');

                        if (!empty($listing) || !empty($property)) {
                            $this->addForcedRegistration($property ?: $listing, $registration_required);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add forced verification code to the page
     *
     * @return void
     */
    abstract protected function addForcedVerification();

    /**
     * Add forced registration code to the page
     *
     * @param array $listing
     * @param bool $registration_required
     * @return void
     */
    abstract protected function addForcedRegistration(array $listing, $registration_required);

    /**
     * Implement this method and place code here to create agent site pages
     * @param int $agentId
     * @throws \Exception
     * @return bool Should we insert the standard pages?
     */
    public function insertAgentSitePages($agentId)
    {
        return true;
    }

    /**
     * Insert framework snippets for agent subdomain site
     * @see Installer::getSnippets
     * @param int $agentId
     * @param array $excludeSnippets
     * @throws \Exception
     * @return bool Should we insert the standard snippets?
     */
    public function insertAgentSiteSnippets($agentId, $excludeSnippets=[])
    {
        return $this->insertSiteSnippets($agentId, 'agent', $excludeSnippets);
    }

    /**
     * Implement this method and place code here to create team site pages
     * @param int $teamId
     * @throws \Exception
     * @return bool Should we insert the standard pages?
     */
    public function insertTeamSitePages($teamId)
    {
        return true;
    }

    /**
     * Implement this method and place code here to create team site snippets
     * @param int $teamId
     * @param array $excludeSnippets
     * @throws \Exception
     * @return bool Should we insert the standard snippets?
     */
    public function insertTeamSiteSnippets($teamId, $excludeSnippets=[])
    {
        return $this->insertSiteSnippets($teamId, 'team', $excludeSnippets);
    }

    /**
     * Insert framework snippets for subdomain site
     * @see Installer::getSnippets
     * @param int $siteId
     * @param string $siteType
     * @param array $excludeSnippets
     * @throws \Exception
     * @return bool Should we insert the standard snippets?
     */
    public function insertSiteSnippets($siteId, $siteType, $excludeSnippets=[])
    {
        $snippets = $this->installer->getSnippets(true);
        if (!empty($snippets) && is_array($snippets)) {
            $db = $this->dbFactory->get('cms');
            try {
                $siteType = preg_replace('/[^a-z0-9_-]/', '', $siteType);

                // Add agent's snippets
                $db->beginTransaction();
                $queryString = "INSERT INTO `snippets`"
                    . " SET `" . $siteType . "` = ?, `name` = ?, `code` = ?, `type` = ?"
                    . ($siteType != 'agent' ? ", `agent` = NULL" : '');
                $query = $db->prepare($queryString);
                static $c=0;
                foreach ($snippets as $snippet) {
                    $name = basename($snippet, '.txt');
                    if (in_array($name, $excludeSnippets)) continue;
                    $code = file_get_contents($snippet);
                    json_decode($code);
                    $type = (json_last_error() == JSON_ERROR_NONE ? 'module' : 'cms');
                    $query->execute([$siteId, $name, $code, $type]);
                    $c++;
                }
                var_dump('installed ' . $c . ' snippets');
                $db->commit();
                return false;

                // Rollback on query error
            } catch (\PDOException $e) {
                $this->log->error($e->getMessage());
                $db->rollBack();
            }
        }
        return true;
    }

    /**
     * Load Extra Skin Modules
     * @param PageInterface $page
     * @return void
     */
    public function loadModules(PageInterface $page)
    {
    }

    /**
     * Load Skin
     * @param string $skin Skin
     * @param string $scheme Scheme
     * @return SkinInterface|null
     * @deprecated Skin should be a singleton... Use container->get(SkinInterface::class) to get the current skin.
     */
    public static function load($skin = null, $scheme = null)
    {
        $container = Container::getInstance();
        if ($skin === null && $scheme === null) {
            return $container->get(SkinInterface::class);
        }

        $class = self::getClass($skin);
        if (class_exists($class) || $container->has($class)) {
            return $container->make($class, ['scheme' => $scheme ?: 'default']);
        }
        return null;
    }

    /**
     * Checks if the loaded skin has the requested feature.
     * @param string $feature
     * @return bool
     */
    public static function hasFeature($feature)
    {
        $class = static::getClass();

        return in_array($feature, $class::getFeatures());
    }

    /**
     * Check for Skin File
     * @see Resource::checkFile()
     */
    public function checkFile($file, $path = null)
    {
        $class = get_called_class();
        $path = !is_null($path) ? $path : $this->getPath();
        do {
            $check = parent::checkFile($file, $path);
            if (empty($check)) {
                $parent = get_parent_class($class);
                if (!is_subclass_of($parent, 'Resource')) {
                    break;
                }
                $path = str_replace($_SERVER['DOCUMENT_ROOT'], '%DOCUMENT_ROOT%', $path);
                $path = str_replace(DIRECTORY_SEPARATOR . $class::$directory, DIRECTORY_SEPARATOR . $parent::$directory, $path);
                $path = str_replace('%DOCUMENT_ROOT%', $_SERVER['DOCUMENT_ROOT'], $path);
                $class = $parent;
            }
        } while (empty($check));
        return $check;
    }

    /**
     * Check for Skin Folder
     * @see Resource::checkDir()
     */
    public function checkDir($dir, $path = null)
    {
        $class = get_called_class();
        if (is_null($path)) {
            $path = $this->getPath();
        }
        do {
            $check = parent::checkDir($dir, $path);
            if (empty($check)) {
                $parent = get_parent_class($class);
                if (!is_subclass_of($parent, 'Resource')) {
                    break;
                }

                $classObj = (new ReflectionClass($parent));

                if (!$classObj->isAbstract()) {
                    $classObj = (new ReflectionClass($parent))->newInstanceWithoutConstructor();
                    $path = (new ReflectionMethod($classObj, 'getPath'))->invoke($classObj);
                }

                $class = $parent;
            }
        } while (empty($check));
        return $check;
    }

    /**
     * @see Page::container
     */
    public function container($container)
    {
        return $this->page->container($container);
    }

    /**
     * @see Page::variable
     */
    public function variable($var, $data = null)
    {
        return $this->page->variable($var, $data);
    }

    /**
     * Set Page
     * @param PageInterface $page
     * @return SkinInterface
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Set Skin Scheme
     * @param string $scheme
     * @return SkinInterface
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * Get Skin Scheme
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Get Page
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get URL to Skin's Path
     * @return string
     */
    public function getUrl()
    {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->getPath());
    }

    /**
     * Get URL to Favicon
     * @return string
     */
    public function getFaviconUrl()
    {
        if(empty($this->favicon)) {
            try {
                // Get updated CMS settings
                $db = $this->dbFactory->get('cms');
                $query = sprintf(
                    "SELECT * FROM `%s` WHERE `type` = 'favicon';",
                    $this->settings['TABLES']['UPLOADS']
                );
                $result = $db->fetch($query);

                if (!empty($result['file'])) {
                    $uploaded_logo = $result['file'];
                }
            } catch (\Exception $exception) {
                $this->log->error("Exception while writing to CMS uploads table.");
            }

            // Legacy uploaded favicon
            $legacy_logo = '/favicon.ico';

            if (!empty($uploaded_logo) && file_exists($this->settings['DIRS']['UPLOADS'] . $uploaded_logo)) {
                $result_logo = '/uploads/' . $uploaded_logo;
            } else if (file_exists($_SERVER['HTTP_HOST'] . $legacy_logo)) {
                $result_logo = $legacy_logo;
            } else {
                $result_logo = '';
            }
            $this->favicon = $result_logo;
        }
        return $this->favicon;
    }

    /**
     * Get URL to Scheme's Path
     * @return string
     */
    public function getSchemeUrl()
    {
        return $this->getUrl() . '/schemes/' . $this->getScheme();
    }

    /**
     * Get Path to Logo
     * @param string $fileName
     * @return string|null
     */
    public function getLogoPath($fileName)
    {
        if (empty($fileName)) return null;
        $db = $this->dbFactory->get('cms');
        $query = sprintf("SELECT `file` FROM `%s` WHERE `file` LIKE '%s%%' AND `type` = '%s' LIMIT 1;", 'cms_uploads', $fileName, 'cms_settings_logo');
        $result = $db->fetch($query);
        if (!empty($result['file'])) return '/uploads/' . $result['file'];
        return null;
    }


    /**
     * Get Template File
     * @return string
     */
    public function getTemplate()
    {
        $template = $this->getPage()->getTemplate();
        return ($template instanceof ResourceInterface) ? $template->getTemplate() : $this->template;
    }

    /**
     * Get path to page templates
     * @return string
     */
    public function getTemplatePath()
    {
        return 'tpl/pages/';
    }

    /**
     * Get file for page templates
     * @return string
     */
    public function getTemplateFile()
    {
        return 'page.tpl';
    }

    /**
     * Get Skin's Page Templates
     * @param boolean $reload
     * @return array
     */
    public function getTemplates($reload = false)
    {
        if (!is_array($this->templates) || $reload) {
            $this->templates = array();
            $order = array();

            // Get directory to check for page templates
            $templatePath = $this->getTemplatePath();
            $tplDir = $this->checkDir($templatePath);
            if (!empty($tplDir)) {

                // Scan Directories
                foreach (glob($tplDir . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
                    $config = $dir . 'config.json';
                    if (file_exists($config)) {
                        $template = $this->container->make(Page_Template::class, ['path' => $dir]);
                        $this->templates[$template->getName()] = $template;
                        $order[$template->getName()] = $template->getOrder();
                    } else {
                        // Scan Sub-Directories
                        foreach (glob($dir . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
                            $config = $dir . 'config.json';
                            if (file_exists($config)) {
                                $template = $this->container->make(TemplateInterface::class, ['path' => $dir]);
                                $this->templates[$template->getName()] = $template;
                                $order[$template->getName()] = $template->getOrder();
                            }
                        }
                    }
                }
            }
            // Re-Order Templates
            array_multisort($order, SORT_ASC, $this->templates);
        }
        return $this->templates;
    }

    /**
     * Get Selectable Templates
     * @param boolean $reload
     * @return array
     */
    public function getSelectableTemplates($reload = false)
    {
        return array_filter($this->getTemplates($reload), function ($template) {
            return $template->isSelectable();
        });
    }

    /**
     * Get Class Name
     * @param string $skinName
     * @return string|NULL
     */
    public static function getClass($skinName = null)
    {
        if (is_null($skinName)) {
            $skinName = Settings::getInstance()->SKIN;
        }
        $skinName = str_replace('-', '', $skinName);
        if (class_exists($skinName)) {
            return $skinName;
        }
        $patterns = SkinProvider::SKIN_CLASS_PATTERNS;
        $tryClasses = [];
        foreach ($patterns as $pattern) {
            $tryClasses[] = sprintf($pattern, ucwords($skinName));
            $tryClasses[] = sprintf($pattern, strtoupper($skinName));
        }
        $container = Container::getInstance();
        foreach ($tryClasses as $tryClass) {
            if ($container->has($tryClass)) {
                return $tryClass;
            }
        }
        return null;
    }

    /**
     * Get Skin Directory
     * @param string $skin
     * @return string
     */
    public static function getDirectory($skin = null)
    {
        if ($skinClass = self::getClass($skin)) {
            return $skinClass::$directory;
        }
        return $skin;
    }

    /**
     * @return string
     */
    public function getDefaultConsentMessage ()
    {
        return __('Please send me updates concerning this website and the real estate market.');
    }


    /**
     * Load Map API Scripts
     */
    public function loadMapApi()
    {
        // Only Load Once
        if (!empty($this->mapLoaded)) {
            return;
        }
        $this->mapLoaded = true;

        $apiKey = Settings::get('google.maps.api_key');

        // Root Directory
        $dir_root = $_SERVER['DOCUMENT_ROOT'];
        // Javascript Dependencies
        $this->addJavascript('var GOOGLE_API_KEY = ' . json_encode($apiKey), 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.Marker.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.Tooltip.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.Directions.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.Streetview.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.SearchControl.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.RadiusControl.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.PolygonControl.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.MarkerManager.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.MarkerCluster.js', 'map');
        $this->addJavascript($dir_root . '/inc/js/map/Map.MarkerLabel.js', 'map');
    }

    /**
     * Get Name of Skin
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns a list of features that skin uses (used to define whether feature or feature configuration should display)
     * @return array of strings
     */
    public function getFeatures()
    {
        return static::$features;
    }

    /**
     * Gets the file for the backend WYSIWYG editor to consume. Nom nom.
     * @return string
     */
    public static function getWYSIWYGHelperCSSFile()
    {
        $urlBackend = Settings::getInstance()->URLS['URL_BACKEND'];
        return $urlBackend . 'inc/css/tinymce.css';
    }

    /**
     * Renders any critical stylesheets (for skins that support it)
     */
    public function renderCriticalStylesheets()
    {
    }

    /**
     * Renders any async stylesheets (for skins that support it)
     */
    public function renderAsynchronousStylesheets()
    {
    }

    /**
     * Render skin file
     * @param string $file
     * @param array $vars
     */
    public function includeFile($file, $vars = [])
    {
        if ($file = $this->checkFile($file)) {
            extract($vars);
            include $file;
        }
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return 'Modules\\';
    }

    /**
     * Get all module namespaces in order from highest to lowest precedence.
     * @return array
     */
    public function getModuleNamespaces()
    {
        $namespaces = array();
        $skinClass = get_class($this);

        while (is_subclass_of($skinClass, SkinInterface::class)) {
            /** @var ReflectionClass $reflector */
            $reflector = $this->container->make(ReflectionClass::class, ['argument' => $skinClass]);
            $absolutePath = $reflector->getMethod('getAbsolutePath')->invoke($this);
            $namespace = $reflector->getMethod('getModuleNamespace')->invoke($this);

            $namespaces[$absolutePath] = $namespace;

            $skinClass = get_parent_class($skinClass);
        }

        return $namespaces;
    }

    /**
     * Get the absolute path for this skin
     * @param string|null $directory
     * @return string
     */
    public function getAbsolutePath($directory = null)
    {
        if (!$directory) {
            $directory = static::$directory;
        }
        return __DIR__ . '/../skins/' . $directory;
    }

    /**
     * Get Saved Search Email Path
     * @return string
     */
    public function getSavedSearchEmailPath()
    {
        return sprintf("::partials/email-templates/saved-searches/%s/", $this->getDirectory());
    }

    /**
     * Get Saved Search Email Path
     * @return string
     */
    public static function getInstallDirectory($skin = null)
    {
        return sprintf('%s%s', Settings::getInstance()['DIRS']['INSTALL'], self::getDirectory($skin));
    }

    /**
     * Generate Body Class
     * @return string
     */
    public function bodyClass()
    {
        // TODO: Implement bodyClass() method.
    }
}
