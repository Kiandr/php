<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\ContextualModuleInterface;
use REW\Core\Interfaces\ModuleControllerInterface;
use REW\Core\Interfaces\NamespaceContainerInterface;
use REW\Core\Interfaces\Page\ContainerInterface as PageContainerInterface;
use REW\Contracts\View\Factory as ViewFactory;

/**
 * Module
 */
class Module extends Resource implements ModuleInterface, ContextualModuleInterface
{

    /**
     * Namespace & path pattern to locate via ViewFactory
     * @var string
     */
    const TWIG_MODULE_PATH = 'theme::module/%s/%s';

    /**
     * Path to theme scripts
     * @var string
     */
    const SCRIPT_MODULE_PATH = 'scripts/module/%s/%s';

    /**
     * Path to theme styles
     * @var string
     */
    const STYLE_MODULE_PATH = 'styles/module/%s/%s';

    /**
     * Module Count
     * @var array
     */
    protected static $count = array();

    /**
     * Module ID
     * @var string
     */
    protected $id;

    /**
     * Unique Module ID
     * @var string
     */
    protected $uid;

    /**
     * Module Container
     * @var Page_Container
     */
    protected $container;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Module Output
     * @var string
     */
    protected $output;

    /* Module Resource Files */
    public $fileConfig      = 'config.ini.php';
    public $fileController  = 'module.php';
    public $fileTemplate    = 'module.tpl.php';
    public $fileStylesheet  = 'module.css.php';
    public $fileJavascript  = 'module.js.php';
    public $fileTplHead     = '_module_head.tpl.php';
    public $fileTplFoot     = '_module_foot.tpl.php';

    /**
     * CSS Code
     * @var string
     */
    public $css;

    /**
     * Javascript Code
     * @var string
     */
    public $javascript;

    /**
     * @var ContainerInterface
     */
    public $diContainer;

    /**
     * @var
     */
    private $namespaceContainer;

    /**
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * @var mixed
     */
    private $context;

    /**
     * @var array NamespaceContainer
     */
    private static $autoloadPaths = null;

    /**
     * Create Module
     *
     * @param string $id Module ID
     * @param array $config Override default settings from $this->fileConfig
     * @param ContainerInterface $diContainer
     * @param SettingsInterface $settings
     * @param NamespaceContainerInterface $namespaceContainer = null
     * @param ViewFactory $viewFactory
     * @return void
     */
    public function __construct(
        $id,
        $config = array(),
        ContainerInterface $diContainer = null,
        SettingsInterface $settings = null,
        NamespaceContainerInterface $namespaceContainer = null,
        ViewFactory $viewFactory = null
    ) {
        if ($diContainer === null) {
            $diContainer = Container::getInstance();
        }
        if ($settings === null) {
            $settings = $diContainer->get(SettingsInterface::class);
        }
        if ($namespaceContainer === null) {
            $namespaceContainer = $diContainer->get(NamespaceContainerInterface::class);
        }
        if ($viewFactory === null) {
            $viewFactory = $diContainer->get(ViewFactory::class);
        }

        $this->diContainer = $diContainer;
        $this->settings = $settings;
        $this->namespaceContainer = $namespaceContainer;
        $this->viewFactory = $viewFactory;

        // Increment Count (Appended to UID)
        $count = ++self::$count[$id];

        if ($count == 1) {
            // Store the first module of this name for re-use.
            $this->diContainer->set($id, $this);
        }

        // Module ID
        $this->id = $id;

        // Path Defined
        if (!empty($config['path'])) {
            $this->config('path', $config['path']);
        }

        // Config file
        if (isset($config['config'])) {
            $this->fileConfig = $config['config'];
        }

        // Locate Settings
        $settings = $this->locateFile($this->fileConfig);
        if (!empty($settings)) {
            // Load Module Settings
            $settings = parse_ini_file($settings);

            // File Locations
            if (isset($settings['controller'])) {
                $this->fileController = $settings['controller'];
            }
            if (isset($settings['template'])) {
                $this->fileTemplate   = $settings['template'];
            }
            if (isset($settings['stylesheet'])) {
                $this->fileStylesheet = $settings['stylesheet'];
            }
            if (isset($settings['javascript'])) {
                $this->fileJavascript = $settings['javascript'];
            }

            // Load Settings
            $this->config($settings);
        }

        /* Configuration Settings */
        if (!empty($config)) {
            /* File Locations */
            if (isset($config['controller'])) {
                $this->fileController = $config['controller'];
            }
            if (isset($config['template'])) {
                $this->fileTemplate   = $config['template'];
            }
            if (isset($config['stylesheet'])) {
                $this->fileStylesheet = $config['stylesheet'];
            }
            if (isset($config['javascript'])) {
                $this->fileJavascript = $config['javascript'];
            }

            /* Load Settings */
            $this->config($config);
        }

        // Unique Module ID
        if (!empty($config['uid'])) {
            $this->uid = $config['uid'];

            // Load UID from Config
        } elseif (!empty($this->config['uid'])) {
            $this->uid = $this->config['uid'] . '_' . $count;

            // Create UID
        } else {
            $this->uid = $id . '_' . $count; // Must be Unique
        }
    }

    /**
     * Set Module's Container
     *
     * @param PageContainerInterface $container
     * @return void
     */
    public function setContainer(PageContainerInterface &$container)
    {
        $this->container = $container;
    }

    /**
     * Get Module's Container
     *
     * @return PageContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get Module's Container's Page
     *
     * @return PageInterface|NULL
     */
    public function getPage()
    {
        if ($container = $this->getContainer()) {
            return $container->getPage();
        }
        return null;
    }

    /**
     * Get Module ID
     *
     * @return string $this->id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Module Unique ID
     *
     * @return string $this->uid
     */
    public function getUID()
    {
        return $this->uid;
    }

    /**
     * Get CSS Code
     *
     * @return string    CSS Code
     */
    public function css()
    {
        // Get Theme Stylesheet
        $page = $this->diContainer->get(PageInterface::class);
        $file = str_replace('.css.php', '.css', $this->fileStylesheet);
        $stylesheet = $page->getSkin()->checkFile(sprintf(self::STYLE_MODULE_PATH, $this->id, $file));

        // Get Core Stylesheet
        if (empty($stylesheet)) {
            $stylesheet = $this->locateFile($this->fileStylesheet, null);
        }

        // Return Stylesheet
        if (!empty($stylesheet)) {
            ob_start();
            include $stylesheet;
            $this->css .= ob_get_clean();
        }
        return $this->css;
    }

    /**
     * Get Javascript Code
     *
     * @return string    Javascript Code
     */
    public function javascript()
    {
        // Get Theme Script
        $page = $this->diContainer->get(PageInterface::class);
        $file = str_replace('.js.php', '.js', $this->fileJavascript);
        $javascript = $page->getSkin()->checkFile(sprintf(self::SCRIPT_MODULE_PATH, $this->id, $file));

        // Get Core Script
        if (empty($javascript)) {
            $javascript = $this->locateFile($this->fileJavascript, null);
        }

        // Return Script
        if (!empty($javascript)) {
            ob_start();
            include $javascript;
            $this->javascript .= ob_get_clean();
        }
        return $this->javascript;
    }

    /**
     * Display Module
     * @param bool $output    Output HTML or Return HTML
     * @return mixed
     */
    public function display($output = true)
    {
        // Only Run Once...
        if (empty($this->output)) {
            // Profile start
            $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->setDetails('<strong>' . $this->getId() . '</strong><br>')->start();
            $included = array();
            // Output Buffer
            ob_start();

            if ($controllerAlias = $this->getBoundControllerAlias()) {
                /** @var ModuleControllerInterface $controller */
                $controller = $this->diContainer->make($controllerAlias, ['module' => $this, 'config' => $this->config]);
                $contextual = false;
                if ($controller instanceof ContextualModuleInterface) {
                    $controller->setContext($this->context);
                    $contextual = true;
                }

                $controller->display(false);

                if ($contextual) {
                    $controller->clearContext();
                }
            } else {
                foreach ($this->getRequiredFiles() as $file) {
                    $included[] = $file;
                    if (is_file($file)) {
                        include $file;
                    } elseif ($this->viewFactory->exists($file)) {
                        echo $this->viewFactory->render($file, get_defined_vars());
                    }
                }
            }

            // Module Output
            $this->output = ob_get_clean();
            // Profile end
            $timer->appendDetails('<code>' . implode('</code><br><code>', $included) . '</code>')->stop();
        }
        if (!empty($output)) {
            echo $this->output;
        } else {
            return $this->output;
        }
    }

    /**
     * Gets all files in order to properly display this module.
     * @return Generator
     */
    public function getRequiredFiles()
    {
        // Profile start
        $timer = Profile::timer()->stopwatch('<code>' . __METHOD__ . '</code>')->setDetails('<strong>' . $this->getId() . '</strong><br>')->start();
        $required = array();

        // Locate Controller
        $controller = $this->locateFile($this->fileController);
        if (!empty($controller)) {
            $required[] = $controller;
            yield $controller;
        }

        // Locate template in theme namespace before modules folder
        $twigTemplate = str_replace('.tpl.php', '', $this->fileTemplate);
        $twigTemplateFile = sprintf(self::TWIG_MODULE_PATH, $this->getId(), $twigTemplate);
        if ($this->viewFactory->exists($twigTemplateFile)) {
            $template = $twigTemplateFile;
        // Fallback to module html
        } else {
            $template = $this->locateFile($this->fileTemplate);
        }

        if (!empty($this->config['wrap_module'])) {
            $header = $_SERVER['DOCUMENT_ROOT'] . '/inc/modules/' . $this->fileTplHead;
            if (is_file($header)) {
                $required[] = $header;
                yield $header;
            }
        }
        if (!empty($template)) {
            $required[] = $template;
            yield $template;
        }
        if (!empty($this->config['wrap_module'])) {
            $footer = $_SERVER['DOCUMENT_ROOT'] . '/inc/modules/' . $this->fileTplFoot;
            if (is_file($footer)) {
                $required[] = $footer;
                yield $footer;
            }
        }

        // Profile end
        $timer->appendDetails('<code>' . implode('</code><br><code>', $required) . '</code>')->stop();
    }

    /**
     * Locate File
     *
     * @param string $file   File Name
     * @param string $skip   Skip File
     * @return false|string
     */
    public function locateFile($file, $skip = false)
    {
        // Files to Locate
        $files = array();
        // Config Path
        $path = $this->config('path');
        if (!empty($path)) {
            $files[] = $path . Settings::getInstance()->LANG . '.' . $file;
            $files[] = $path . $file;

            // Find file in this skin's config path
            $skin_path = $path . '/{skin}/{id}/';
            $this->locateSkinFiles($file, $skin_path, $files);

            // Find file in this config path
            $path .= $this->id . '/';
            if (is_dir($path)) {
                $files[] = $path . Settings::getInstance()->LANG . '.' . $file;
                $files[] = $path . $file;
            }
        }
        // Find file in skin paths
        $skin_path = $_SERVER['DOCUMENT_ROOT'] . '/inc/skins/{skin}/modules/{id}/';
        $this->locateSkinFiles($file, $skin_path, $files);
        // Core Module
        $path = $_SERVER['DOCUMENT_ROOT'] . '/inc/modules/' . $this->id . '/';
        if (is_dir($path)) {
            $files[] = $path . Settings::getInstance()->LANG . '.' . $file;
            $files[] = $path . $file;
        }
        // Locate File
        foreach ($files as $file) {
            if (!empty($skip) && $skip === $file) {
                continue;
            }
            if (is_file($file)) {
                return $file;
            }
        }
        // Not Found
        return false;
    }

    /**
     * @see Resource::addStylesheet
     */
    public function addStylesheet($source, $group = 'modules', $minify = true)
    {
        return $this->addSource(Source_Type::STYLESHEET, $source, $group, $minify);
    }

    /**
     * @see Resource::addJavascript
     */
    public function addJavascript($source, $group = 'modules', $minify = true, $async = false)
    {
        return $this->addSource(Source_Type::JAVASCRIPT, $source, $group, $minify);
    }

    /**
     * @see Resource::addSource
     */
    public function addSource($type, $source, $group = 'modules', $minify = true, $async = false)
    {
        return $this->container->getPage()->addSource($type, $source, $group, $minify);
    }

    /**
     * Locate File
     *
     * @param string $file   File Name
     * @param string $default_path Path to the skin dir.
     * {state} and {id} must be set in default path.
     * @param array $files Mutatable array for files
     * @return bool
     */
    private function locateSkinFiles($file, $default_path, array &$files)
    {

        // throw an Exception
        if (false === strpos($default_path, '{state}') && false === strpos($default_path, '{id}')) {
            throw new Exception('State or ID was not set in default path.');
        }

        // Get path to default Skin
        $skin = Skin::getClass(Settings::getInstance()->SKIN);

        do {
            $skin = !empty($skin) ? $skin : Settings::getInstance()->SKIN;
            $path = str_replace(['{skin}','{id}'], [Skin::getDirectory($skin), $this->id], $default_path);
            if (is_dir($path)) {
                $files[] = $path . Settings::getInstance()->LANG . '.' . $file;
                $files[] = $path . $file;
            }
            $skin = get_parent_class($skin);
        } while (is_subclass_of($skin, 'Skin'));

        // Located File
        return true;
    }

    /**
     * Get a usable controller alias for this module if possible. This method MUST return something that we can build
     * using our factory. That is, it must have no un-guessable arguments besides $module.
     * @return string|null
     */
    public function getBoundControllerAlias()
    {
        $controllerAlias = sprintf(self::PATTERN_BIND, $this->id);

        if (!$this->diContainer->has($controllerAlias)) {
            $controllerAlias = null;
            $controllerClassName = Format::camelCase($this->id);

            foreach ($this->namespaceContainer->getModuleNamespaces() as $ns) {
                $tryClass = $ns . $controllerClassName . '\\' . $controllerClassName . 'Module';
                if (class_exists($tryClass)) {
                    $controllerAlias = $tryClass;
                    break;
                }
            }
        }

        if ($controllerAlias && $this->diContainer->has($controllerAlias)) {
            return $controllerAlias;
        }

        return null;
    }

    /**
     * Bind a module to a class
     *
     * @param string $className Must be absolute
     * @return string|null
     */
    public function bindController($className)
    {
        $bindName = sprintf(self::PATTERN_BIND, $this->getId());
        $oldController = $this->getBoundControllerAlias();
        $this->diContainer->set($bindName, $className);

        return $oldController;
    }

    /**
     * @inheritDoc
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function clearContext()
    {
        $this->context = null;
    }

    /**
     * Add path to use for a given namespace. Register the autoloader on first use.
     * @param string $namespace
     * @param string $path
     */
    public static function addPathForNamespace($namespace, $path)
    {
        if (!isset(static::$autoloadPaths)) {
            spl_autoload_register([self::class, 'resolveClass']);
        }

        if (!isset(self::$autoloadPaths[$namespace])) {
            self::$autoloadPaths[$namespace] = array();
        }

        if (!in_array($path, self::$autoloadPaths[$namespace])) {
            self::$autoloadPaths[$namespace][] = $path;
        }
    }

    /**
     * Try to resolve the class provided by $className
     * @param string $className
     */
    public static function resolveClass($className)
    {
        $rootNamespace = static::MODULE_ROOT_NAMESPACE;
        $classComponents = explode('\\', $className);

        $moduleName = null;
        $modulePath = null;
        if ($classComponents[0] === $rootNamespace) {
            // Remove namespace
            array_shift($classComponents);
            if (!empty($classComponents)) {
                array_pop($classComponents);
                $modulePath = array_pop($classComponents);
                $moduleName = Format::snakeCase($modulePath);
            }
        }

        if (!$moduleName) {
            return;
        }

        foreach (self::$autoloadPaths as $namespace => $paths) {
            $namespaceLength = strlen($namespace);
            if (substr($className, 0, $namespaceLength) == $namespace) {
                $tryClassPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, $namespaceLength));
                foreach ($paths as $path) {
                    $tryFileNames = array();

                    $tryFileNames[] = $path . DIRECTORY_SEPARATOR . $tryClassPath . '.php';

                    $tryClassPath = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, $namespaceLength + strlen($moduleName)));
                    array_unshift($tryFileNames, $path . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . $tryClassPath . '.php');

                    foreach ($tryFileNames as $tryFileName) {
                        if (file_exists($tryFileName)) {
                            include $tryFileName;

                            if (class_exists('\\' . $className, false)) {
                                // We found the class, yay!
                                return;
                            }
                        }
                    }
                }
            }
        }
    }
}
