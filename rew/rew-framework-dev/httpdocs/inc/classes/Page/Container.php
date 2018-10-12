<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\ConfigurableInterface;
use REW\Core\Interfaces\ContextualModuleInterface;
use REW\Core\Interfaces\Page\ContainerInterface as PageContainerInterface;

/**
 * Page_Container
 */
class Page_Container implements ConfigurableInterface, PageContainerInterface
{

    /**
     * Configuration Settings
     * @var array
     */
    protected $config  = array();

    /**
     * Unique Container ID
     * @var string
     */
    protected $id;

    /**
     * Container Page
     * @var PageInterface
     */
    protected $page;

    /**
     * Container Modules
     * @var array
     */
    protected $modules = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Create Page_Container
     *
     * @param string $id       Unique Container ID
     * @param array $config    Container Settings
     * @param ContainerInterface $container The DI container
     */
    public function __construct($id, $config = array(), ContainerInterface $container = null)
    {
        if ($container === null) {
            $container = Container::getInstance();
        }

        $this->id = $id;
        $this->container = $container;

        $this->config($config);
    }

    /**
     * Set Page
     *
     * @param PageInterface $page
     * @return void
     */
    public function setPage(PageInterface &$page)
    {
        $this->page = $page;
    }

    /**
     * Get Page
     *
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Remove Module
     *
     * @param mixed $module    Module ID or Module Instance
     * @return void
     */
    public function removeModule($module)
    {
        if ($module instanceof ModuleInterface) {
            $module = $module->getId();
        }
        foreach ($this->modules as $index => $modules) {
            if ($modules->getId() == $module) {
                unset($this->modules[$index]);
            }
        }
    }

    /**
     * Add Module
     *
     * @param mixed $module    Module ID or Module Instance
     * @param array $config    Module Settings
     * @return ModuleInterface
     */
    public function addModule($module, $config = array())
    {
        if (!($module instanceof ModuleInterface)) {
            // Create Module
            $module = $this->container->make(ModuleInterface::class, ['id' => $module, 'config' => $config]);
        }
        $module->setContainer($this);
        if (!empty($config['prepend'])) {
            array_unshift($this->modules, $module);
        } else if (!empty($config['index'])) {
            $this->modules[$config['index']] = $module;
        } else {
            $this->modules[] = $module;
        }
        return $module;
    }

    /**
     * Check if container contains certain module
     *
     * @param string $module    Module ID
     * @return false|ModuleInterface
     */
    public function contains($module)
    {
        foreach ($this->modules as $index => $modules) {
            if ($modules->getId() == $module) {
                return $this->modules[$index];
            }
        }
        return false;
    }

    /**
     * Select ModuleInterface Object, Create if it does not exist
     *
     * @param string $module       Module ID
     * @param array $config    Module Settings
     * @return ModuleInterface
     */
    public function module($module, $config = array())
    {
        if ($module instanceof ModuleInterface) {
            return $this->addModule($module, $config);
        }
        return $this->addModule($module, $config);
    }

    /**
     * Get Modules
     *
     * @return array
     */
    public function fetchModules()
    {
        return $this->modules;
    }

    /**
     * Count Modules
     *
     * @return integer
     */
    public function countModules()
    {
        return count($this->modules);
    }

    /**
     * Get Containers ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Load & Display Container's Moudles
     * @param bool $output Output HTML or Return HTML
     * @param mixed $context The context to pass to each module during render
     * @return string
     */
    public function loadModules($output = true, $context = ContextualModuleInterface::CONTEXT_DEFAULT)
    {
        $return = '';
        if (!empty($this->modules)) {
            ksort($this->modules);
            foreach ($this->modules as $k => $module) {
                if ($module instanceof ModuleInterface) {
                    $contextual = false;
                    if ($module instanceof ContextualModuleInterface) {
                        $module->setContext($context);
                        $contextual = true;
                    }
                    $return .= $module->display($output);
                    if ($contextual) {
                        $module->clearContext();
                    }
                }
            }
        }
        return $return;
    }

    /**
     * @see ConfigurableInterface::config()
     */
    public function config($option, $value = null)
    {
        // Get Configuration Setting
        if (is_string($option) && is_null($value)) {
            return $this->config[$option];
        }
        // Set Configuration Setting
        if (is_string($option) && !is_null($value)) {
            $this->config[$option] = $value;
            return $this;
        }
        // Overwrite Configuration
        if (is_array($option)) {
            foreach ($option as $setting => $value) {
                $this->config[$setting] = $value;
            }
            return $this;
        }
    }

    /**
     * @see ConfigurableInterface::getConfig()
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @deprecated Use self::countModules Instead
     */
    public function ModuleCount()
    {
        return $this->countModules();
    }
}
