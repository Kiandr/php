<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\CacheInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\Page\TemplateInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\SnippetFactoryInterface;
use REW\Core\Interfaces\Page\ContainerInterface as PageContainerInterface;
use REW\Contracts\View\Factory as FactoryInterface;

/**
 * Page
 *
 */
class Page extends Resource implements PageInterface, BackendInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * @var string
     */
    const PAGE_TEMPLATE_PATTERN = 'theme::layout/%s/page';

    /**
     * @var string
     */
    const ROUTE_PATTERN = 'theme::route/%s/%s';

    /**
     * Page Skin
     * @var SkinInterface
     */
    protected $skin;

    /**
     * Page Information
     * @var array
     */
    protected $info  = array();

    /**
     * Page Containers
     * @var array
     */
    protected $containers = array();

    /**
     * Page Template
     * @var TemplateInterface
     */
    protected $template;

    /**
     * Page Variables
     * @var array
     */
    protected $variables;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Snippet
     * @var SnippetFactoryInterface
     */
    protected $snippet;

    /**
     * Factory builder
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * Create Page
     *
     * @param array $config       Page Settings
     * @param ContainerInterface $container The DI container object
     * @param SkinInterface $skin Skin object
     * @param DBFactoryInterface $dbFactory The db factory object
     * @param SettingsInterface $settings The settings object
     * @param HooksInterface $hooks
     * @param SnippetFactoryInterface $snippetFactory
     */
    public function __construct(
        $config = array(),
        ContainerInterface $container = null,
        SkinInterface $skin = null,
        DBFactoryInterface $dbFactory = null,
        SettingsInterface $settings = null,
        HooksInterface $hooks = null,
        SnippetFactoryInterface $snippetFactory = null
    ) {
        if ($container === null) {
            $container = Container::getInstance();
        }
        if ($skin === null) {
            $skin = $container->get(SkinInterface::class);
        }
        if ($dbFactory === null) {
            $dbFactory = $container->get(DBFactoryInterface::class);
        }
        if ($settings === null) {
            $settings = $container->get(SettingsInterface::class);
        }
        if ($hooks === null) {
            $hooks = $container->get(HooksInterface::class);
        }
        if ($snippetFactory === null) {
            $snippetFactory = $container->get(SnippetFactoryInterface::class);
        }

        $this->container = $container;

        // Set Configuration
        $this->config($config);

        // Skin & Scheme
        $this->skin = $skin;
        $skin->setPage($this);

        // Database factory
        $this->dbFactory = $dbFactory;


        // Settings
        $this->settings = $settings;

        // Hooks
        $this->hooks = $hooks;

        // Snippet factory
        $this->snippet = $snippetFactory;
    }

    /**
     * Get / Set Page Information
     *
     * @param string $info    Index Name
     * @param mixed $value    (Optional) Data to Store
     * @return mixed
     */
    public function info($info, $value = null)
    {
        // Get Page Information
        if (is_string($info) && is_null($value)) {
            return $this->info[$info];
        }
        // Set Page Information
        if (is_string($info) && !is_null($value)) {
            $this->info[$info] = $value;
            return $this;
        }
        // Overwrite Information
        if (is_array($info)) {
            foreach ($info as $setting => $value) {
                $this->info[$setting] = $value;
            }
            return $this;
        }
    }

    /**
     * Get / Set Page Variable
     *
     * @param string $var    Variable Name
     * @param mixed $data   (Optional) Variable Data
     * @return mixed
     */
    public function variable($var, $data = null)
    {
        // Get Page Variable
        if (is_string($var) && is_null($data)) {
            return $this->variables[$var];
        }
        // Set Page Variable
        if (is_string($var) && !is_null($data)) {
            $this->variables[$var] = $data;
            return $this;
        }
    }

    /**
     * Display Page
     * @see Resource::display
     */
    public function display($return = false)
    {

        // Skin Path Settings
        $skin = $this->getSkin();
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $skin->getPath());
        $this->config('skin_path', $path);
        $this->config('scheme_path', $path . '/schemes/' . $skin->getScheme());

        // Display Skin
        return $this->skin->display($return);
    }

    /**
     *
     * Load Page Files
     *
     * @param string $app     Application Name
     * @param string $file    Path to File
     * @param string $feed    (Optional) IDX Feed Name
     * @return array $row     Page Row
     * @global array $listing
     * @global array $_COMPLIANCE
     */
    public function load($app, $file, $feed = null)
    {

        // Profile stopwatch
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();

        // Global Resources
        global $listing, $_COMPLIANCE;

        $page = $this;

        // File Exists
        $exists = false;

        // Load Page Template
        $this->loadTemplate();

        // CMS Database
        $db = $this->dbFactory->get('cms');

        // CMS Defaults
        $row = $db->prepare("SELECT *, NULL AS `variables` FROM `default_info` WHERE `agent` <=> :agent AND `team` <=> :team LIMIT 1;");
        $row->execute(array(
            'agent' => $this->settings['SETTINGS']['agent'],
            'team' => $this->settings['SETTINGS']['team']
        ));
        $row = $row->fetch();

        // Overwritable Information
        $page_title     = false;
        $meta_desc      = false;
        $meta_keyw      = false;

        // Set IDX Feed
        if (!empty($feed)) {
            $_feed = $this->settings['IDX_FEED'];
            $this->settings['IDX_FEED'] = $feed;
        }

        // Start Output Buffering
        ob_start();

        // App Settings
        $appPath = $this->settings['DIRS']['ROOT'] . $app . '/';

        // Load Common File
        $common = $appPath . 'common.inc.php';
        if (is_file($common)) {
            include $common;
        }

        // Find PHP File
        $phpFile = $this->locateController($app, 'pages', $file);
        if (!empty($phpFile)) {
            $c_timer = Profile::timer()->stopwatch('Include <code>' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $phpFile) . '</code>')->start();
            require $phpFile;
            $c_timer->stop();
            $exists = true;
        }

        // Load Page Modules
        if (empty($_REQUEST['snippet'])) {
            $this->loadModules();
        }

        // Find TPL File
        $templateFile = $this->locateTemplate($app, 'pages', $file);

        // Include TPL File
        if (!empty($templateFile)) {
            $viewFactory = $this->container->get(FactoryInterface::class);
            $t_timer = Profile::timer()->stopwatch('Include <code>' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $tplFile) . '</code>')->start();
            if (is_file($templateFile)) {
                require $templateFile;
            } elseif ($viewFactory->exists($templateFile)) {
                echo $viewFactory->render($templateFile, get_defined_vars());
            }
            $t_timer->stop();
            $exists = true;
        }

        // Reset IDX Feed
        if (!empty($feed) && !empty($_feed)) {
            $this->settings['IDX_FEED'] = $_feed;
        }

        // 404 - Page not found
        if (empty($exists)) {
            header('HTTP/1.1 404 NOT FOUND');

            // Get 404 Page
            $emptyWhere = [];
            $emptyWhere[] = 'BINARY `file_name` = \'404\'';
            $emptyWhere[] = !empty($this->settings['SETTINGS']['agent']) ? '`agent` <=> \'' . $this->settings['SETTINGS']['agent'] . '\'' : '`agent` IS NULL';
            $emptyWhere[] = !empty($this->settings['SETTINGS']['team']) ? '`team` <=> \'' . $this->settings['SETTINGS']['team'] . '\'' : '`team` IS NULL';
            $emptyWhere = implode(' AND ', $emptyWhere);
            $row = $db->fetch(sprintf("SELECT * FROM `pages` WHERE %s;", $emptyWhere));

            if (!empty($row)) {
                return $row;
            } else {
                echo 'Page not Found';
            }
        }

        // Grab All Output
        $row['category_html'] = ob_get_clean();

        // Meta Information
        if (!empty($page_title)) {
            $row['page_title']        = $page_title;
        }
        if (!empty($meta_desc)) {
            $row['meta_tag_desc']     = $meta_desc;
        }

        // Set timer details and stop it
        $timer->setDetails('<code>' . implode('</code><br><code>', array_filter(array($phpFile, $tplFile))) . '</code>')->stop();

        // Return Output
        return $row;
    }

    /**
     * Locate Controller File
     *
     * @param string $app
     * @param string $type
     * @param string $file
     * @return string|false
     */
    public function locateController($app, $type, $file)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(PageInterface::class, __FUNCTION__, func_get_args());
        }

        $phps = array();
        $path = $this->settings['DIRS']['ROOT'] . $app . '/inc/php/' . $type . '/';
        $feed = $this->settings['IDX_FEED'];
        $skin = $this->settings['SKIN'];
        $skin = Skin::getClass($skin);
        while (is_subclass_of($skin, 'Skin')) {
            if (strstr($app, 'idx') !== false) {
                $phps[] = $path . Skin::getDirectory($skin) . DIRECTORY_SEPARATOR . $feed . DIRECTORY_SEPARATOR . $file . '.php';
            }
            $phps[] = $path . Skin::getDirectory($skin) . DIRECTORY_SEPARATOR . $file . '.php';
            $skin = get_parent_class($skin);
        }
        if (strstr($app, 'idx') !== false) {
            $phps[] = $path . $feed . DIRECTORY_SEPARATOR . $file . '.php';
        }
        $phps[] = $path . $file . '.php';
        return $this->locateFile($phps);
    }

    /**
     * Locate Template File
     *
     * @param string $app
     * @param string $type
     * @param array ...$file
     * @return string|false
     */
    public function locateTemplate($app, $type, ...$file)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(PageInterface::class, __FUNCTION__, func_get_args());
        }

        $file = implode(DIRECTORY_SEPARATOR, $file);

        $viewFactory = $this->container->get(FactoryInterface::class);
        $twigTemplateFile = sprintf(self::ROUTE_PATTERN, $app, ($file === $app ? 'index' : $file));
        if ($viewFactory->exists($twigTemplateFile)) {
            return $twigTemplateFile;
        }

        $tpls = array();
        $path = $this->settings['DIRS']['ROOT'] . $app . '/inc/tpl/' . $type . '/';
        $feed = $this->settings['IDX_FEED'];

        // Email TPLs
        if ($type == 'emails') {
            if (strstr($app, 'idx') !== false) {
                $tpls[] = $path . $feed . DIRECTORY_SEPARATOR . $file . '.tpl';
            }
            $tpls[] = $path . $file . '.tpl';
        // Page & Misc TPLs
        } else {
            $skin = get_class($this->skin);
            if (empty($skin)) {
                $skin = $this->settings['SKIN'];
                if (strstr($app, 'idx') !== false) {
                    $tpls[] = $path . $skin . DIRECTORY_SEPARATOR . $feed . DIRECTORY_SEPARATOR . $file . '.tpl';
                }
                $tpls[] = $path . $skin . DIRECTORY_SEPARATOR . $file . '.tpl';
                $skin = 'Skin';
            }
            while (is_subclass_of($skin, SkinInterface::class)) {
                if (strstr($app, 'idx') !== false) {
                    $tpls[] = $path . Skin::getDirectory($skin) . DIRECTORY_SEPARATOR . $feed . DIRECTORY_SEPARATOR . $file . '.tpl';
                }
                $tpls[] = $path . Skin::getDirectory($skin) . DIRECTORY_SEPARATOR . $file . '.tpl';
                $skin = get_parent_class($skin);
            }
        }
        return $this->locateFile($tpls);
    }

    /**
     * Check Files in Array, Return First File That Exists
     *
     * @param array $files Files to Check
     * @return string|false
     */
    public function locateFile(array $files = array())
    {
        foreach ($files as $file) {
            if (empty($file)) {
                continue;
            }
            if (is_file($file)) {
                return $file;
            }
        }
        return false;
    }

    /**
     * Load Page Modules, This is Used to Load Modules Inside of Modules, and Modules that Affect the Page Sources (Extra CSS/JS, Etc..)
     * @return void
     */
    public function loadModules()
    {
        // Load Page Template's Modules
        $template = $this->getTemplate();
        if (!empty($template)) {
            $template->loadModules($this);
        }
        // Load Skin's Modules
        $this->skin->loadModules($this);
        // Load Page Modules
        foreach ($this->containers as $container) {
            $modules = $container->fetchModules();
            if (!empty($modules)) {
                foreach ($modules as $module) {
                    $module->display(false);
                }
            }
        }
    }

    /**
     * Build Page Sources
     * @see Resource::buildSources()
     */
    public function buildSources()
    {

        // Profile start
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->start();

        // Source Code
        $stylesheet = '';
        $javascript = '';

        // Loop through page Containers
        foreach ($this->containers as $container) {
            $modules = $container->fetchModules();
            if (!empty($modules)) {
                foreach ($modules as $module) {
                    $stylesheet .= $module->css();
                    $javascript .= $module->javascript();
                }
            }
        }

        // Expire time for cache (365 days)
        $cacheExpires = (60 * 60 * 24 * 365);

        // Save Stylesheet for Modules
        if (!empty($stylesheet)) {
            $type = Source_Type::STYLESHEET;
            $hash = md5($stylesheet);
            $cache = $this->container->make(CacheInterface::class, array('options' => array(
                'name'      => $type::$extension . DIRECTORY_SEPARATOR . 'modules.' . $hash . '.min.' . $type::$extension,
                'expires'   => $cacheExpires
            )));
            if ($cache->checkCache() || $cache->save($cache->getName(), $type::minify($stylesheet))) {
                $this->skin->addSource(Source_Type::STYLESHEET, new Source_File($cache->getPath() . $cache->getName(), Source_Type::STYLESHEET), 'modules');
            }
        }

        // Save Javascript for Modules
        if (!empty($javascript)) {
            $type = Source_Type::JAVASCRIPT;
            $hash = md5($javascript);
            $cache = $this->container->make(CacheInterface::class, array('options' => array(
                'name'      => $type::$extension . DIRECTORY_SEPARATOR . 'modules.' . $hash . '.min.' . $type::$extension,
                'expires'   => $cacheExpires
            )));
            if ($cache->checkCache() || $cache->save($cache->getName(), $type::minify($javascript))) {
                $this->skin->addSource(Source_Type::JAVASCRIPT, new Source_File($cache->getPath() . $cache->getName(), Source_Type::JAVASCRIPT), 'modules');
            }
        }

        // Profile end
        Profile::memory()->snapshot(__METHOD__);
        $timer->stop();

        // Build Sources
        parent::buildSources();
    }

    /**
     * Add Container to Page
     *
     * @param mixed $container    Container ID or Container Instance
     * @param array $config       Container Settings
     * @return PageContainerInterface
     */
    public function addContainer($container, $config = array())
    {
        // Create Container if Needed
        if (!($container instanceof PageContainerInterface)) {
            $container = $this->container->make(
                PageContainerInterface::class,
                ['id' => $container, 'config' => $config]
            );
        }
        // Set Page
        $container->setPage($this);
        // Add to Collection
        $this->containers[$container->getId()] = $container;
        // Return Page_Container
        return $container;
    }

    /**
     * Select PageContainerInterface, Create if it does not exist.
     *
     * @param string $container Container ID or Container Instance
     * @param array $config    Container Settings
     * @return PageContainerInterface
     */
    public function container($container, $config = array())
    {
        // Is the container already an object? Add and return it
        if ($container instanceof PageContainerInterface) {
            return $this->addContainer($container, $config);
        }
        // Does this container exist?
        if (!isset($this->containers[$container]) || !($this->containers[$container] instanceof PageContainerInterface)) {
            return $this->addContainer($container, $config);
        }
        // Return Collection
        return $this->containers[$container];
    }

    /**
     * Return all loaded page containers
     *
     * @return array    Page Containers
     */
    public function fetchContainers()
    {
        return $this->containers;
    }

    /**
     * Prepend CSS Code
     *
     * @param string $css CSS Code or CSS File
     * @param string $type 'string' or 'file'
     * @return void
     * @uses Resource::addStylesheet
     */
    public function writeCSS($css, $type = 'string')
    {
        switch ($type) {
            case 'file':
                $this->addStylesheet($this->container
                    ->make(Source_File::class, ['file' => $css, 'type' => Source_Type::STYLESHEET]), 'page');
                break;
            case 'string':
                $this->addStylesheet($this->container
                    ->make(Source_Code::class, ['data' => $css, 'type' => Source_Type::STYLESHEET]), 'page');
                break;
        }
    }

    /**
     * Prepend Javascript Code
     *
     * @param string $javascript Javascript Code or Javascript File
     * @param string $type 'string' or 'file'
     * @return void
     * @uses Resource::addJavascript
     */
    public function writeJS($javascript, $type = 'string')
    {
        switch ($type) {
            case 'file':
                $this->addJavascript($this->container
                    ->make(Source_File::class, ['file' => $javascript, 'type' => Source_Type::JAVASCRIPT]), 'page');
                break;
            case 'string':
                $this->addJavascript($this->container
                    ->make(Source_Code::class, ['data' => $javascript, 'type' => Source_Type::JAVASCRIPT]), 'page');
                break;
        }
    }

    /**
     * Let skin handle sources
     * @see Resource::addSource
     */
    public function addSource($type, $source, $group = 'static', $minify = true, $async = false)
    {
        return $this->skin->addSource($type, $source, $group, $minify, $async);
    }

    /**
     * Check if Module is Loaded
     *
     * @param string $module Module Name
     * @return ModuleInterface|false
     */
    public function moduleLoaded($module)
    {
        foreach ($this->containers as $container) {
            foreach ($container->fetchModules() as $object) {
                if ($module == $object->getId()) {
                    return $object;
                }
            }
        }
        return false;
    }

    /**
     * Get Page's Skin
     * @return SkinInterface
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * Get Page Template
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get Page Variables
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Set Page Template
     * @param string|Page_Template $template
     * @return boolean True on success
     */
    public function setTemplate($template)
    {
        // Load Page Template
        if (!($template instanceof Page_Template)) {

            // Resolve path to specified page template
            $templatePath = $this->skin->getTemplatePath();
            $pathToCheck = sprintf('%s%s/', $templatePath, $template);

            $template = $this->skin->checkDir($pathToCheck);
            if (!empty($template)) {
                $template = $this->container->make(TemplateInterface::class, ['path' => $template]);
            } else {
                // Default Page Template
                $template = $this->skin->getTemplate($this);
                if (!($template instanceof Page_Template)) {
                    $templatePath = $this->skin->getTemplatePath();
                    $pathToCheck = sprintf('%s%s/', $templatePath, $template);
                    $template = $this->skin->checkDir($pathToCheck);
                    if (!empty($template)) {
                        $template = $this->container->make(TemplateInterface::class, ['path' => $template]);
                    }
                }
            }
        }
        // Set Page Template
        if (!empty($template)) {
            $this->template = $template;
            return true;
        }
        // No Template
        return false;
    }

    /**
     * Load Page Template
     * @param string $template
     * @return boolean True on success
     */
    public function loadTemplate($template = null)
    {
        try {

            // No Template Provided
            if (empty($template)) {
                throw new \Exception;
            }

            // Set Template & Load Variables
            return $this->setTemplate($template);

        // Error Occurred - Load Default Template
        } catch (\Exception $e) {
            return $this->setTemplate($this->skin->getTemplate());
        }
    }

    /**
     * Load Page Variables from JSON
     * @param string $json
     * @return void
     */
    public function loadVariables($json)
    {
        $this->variables = array();

        // Load from JSON
        if (!empty($json)) {
            // Parse from JSON String
            $variables = json_decode($json, true);
            if (!empty($variables) && is_array($variables)) {
                $this->variables = $variables;
            }
        }

        // Current page template
        if ($this->template instanceof TemplateInterface) {
            // Execute any current hooks to modify loaded page variables
            $this->variables = $this->hooks->hook(HooksInterface::HOOK_CMS_PAGE_VARIABLES_LOAD)
                ->run($this->variables, $this->template);

            // Load Template Variables
            $this->template->getVariables($this);
        }
    }

}
