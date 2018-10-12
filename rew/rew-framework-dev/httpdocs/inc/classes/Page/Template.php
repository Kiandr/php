<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Page\TemplateInterface;
use \REW\Core\Interfaces\Page\Template\EditorInterface;

/**
 * Page Template
 */
class Page_Template extends Resource implements TemplateInterface
{

    /**
     * Template Title
     * @var string
     */
    protected $title;

    /**
     * Template Description
     * @var string
     */
    protected $description;

    /**
     * Template Variables
     * @var array
     */
    protected $variables;

    /**
     * Is Selectable
     * @var boolean
     */
    protected $selectable = true;

    /**
     * Config Filename
     * @var string
     */
    protected $fileConfig = 'config.json';

    /**
     * Thumb Filename
     * @var string
     */
    protected $fileThumbnail = 'thumb.png';

    /**
     * JavaScript Filename
     * @var string
     */
    protected $fileJavascript = 'page.js.php';

    /**
     * Stylesheet Filename
     * @var string
     */
    protected $fileStylesheet = 'page.less';

    /**
     * Template Filename
     * @var string
     */
    protected $fileTemplate = 'page.tpl';

    /**
     * Template Order
     * @var int
     */
    protected $order;

    /**
     * @var SkinInterface
     */
    protected $skin;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Setup Page Template
     * @param string $path
     * @param SkinInterface $skin
     * @param SettingsInterface $settings
     * @throws Exception If template file is not found
     */
    public function __construct($path, SkinInterface $skin = null, SettingsInterface $settings = null)
    {
        if ($skin === null) {
            $skin = Container::getInstance()->get(SkinInterface::class);
            $this->fileTemplate = $skin->getTemplateFile();
        }

        if ($settings === null) {
            $settings = Container::getInstance()->get(SettingsInterface::class);
        }

        // Load Resource
        parent::__construct($path);

        $this->skin = $skin;
        $this->settings = $settings;

        // Load Configuration
        $config = $this->loadConfig();

        // Page Template
        $this->template = $this->checkFile($this->fileTemplate);
        if (empty($this->template)) {
            throw new Exception('Template error: Could not find ' . $this->fileTemplate);
        }

        // Selectable Template
        $selectable = $config['config']['selectable'];
        if (is_string($selectable)) {
            if (preg_match('/^\{([A-Z\_]+)\}$/', $selectable, $match)) {
                $selectable = !empty($settings['MODULES'][$match[1]]);
            }
        }

        if(isset($config['config']['thumbnail'])){
            $this->fileThumbnail = $config['config']['thumbnail'];
        }

        // Template Config
        $this->title        = $config['config']['title'];
        $this->description  = $config['config']['description'];
        $this->selectable   = !empty($selectable);
        $this->order        = isset($config['config']['order']) ? $config['config']['order'] : $this->order;
    }

    /**
     * Get Template Title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get Template Description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get Template Order
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get Thumbnail File
     * @return string|bool
     */
    public function getThumb()
    {
        $thumbnailFile = $this->fileThumbnail;
        if (strpos($thumbnailFile, '/') === 0) {
            return $thumbnailFile;
        }
        if ($thumb = $this->checkFile($thumbnailFile)) {
            return str_replace($_SERVER['DOCUMENT_ROOT'], '', $thumb);
        }
        return false;

    }

    /**
     * Check if Template is Selectable
     * @return boolean
     */
    public function isSelectable()
    {
        return !empty($this->selectable) ? true : false;
    }

    /**
     * Get Template's Variables
     * @param PageInterface|null $page
     * @param boolean $reload
     * @return array
     */
    public function getVariables(PageInterface $page = null, $reload = false)
    {
        if (!is_array($this->variables) || $reload) {
            // Page Variables
            $variables = array();
            if (!empty($page)) {
                $variables = $page->getVariables();
            }

            // Template Variables
            $this->variables = array();
            if (!empty($this->config['variables']) && is_array($this->config['variables'])) {
                foreach ($this->config['variables'] as $var => $options) {
                    // Load Page Variable
                    $variable = &Page_Variable::load($var, $options);
                    $variable->setTemplate($this);

                    // Variable Data
                    $value = $variables[$var];
                    if (isset($value)) {
                        $variable->setValue($value);
                    }
                    if (!empty($page)) {
                        $page->variable($variable->getName(), $variable->getValue());
                    }

                    // Children Variables
                    $children = $variable->getChildren();
                    if (!empty($children)) {
                        foreach ($children as $child) {
                            $child->setTemplate($this);
                            // Variable Data
                            $value = $variables[$child->getName()];
                            if (isset($value)) {
                                $child->setValue($value);
                            }
                            if (!empty($page)) {
                                $page->variable($child->getName(), $child->getValue());
                            }
                        }
                    }

                    // Add Variable
                    $this->variables[$var] = $variable;
                }
            }
        }
        return $this->variables;
    }

    /**
     * Load Template Modules
     * @param PageInterface $page
     * @return void
     */
    public function loadModules(PageInterface $page)
    {

        // Page Template's Modules
        if (!empty($this->config['containers']) && is_array($this->config['containers'])) {
            foreach ($this->config['containers'] as $c => $modules) {
                if (!empty($modules) && is_array($modules)) {
                    $container = $page->container($c);
                    foreach ($modules as $module => $options) {
                        // Parse module options
                        $options = $this->parseModuleOptions($options, $page);

                        // Skip disabled modules
                        if (isset($options['_enabled']) && empty($options['_enabled'])) {
                            continue;
                        }

                        // Add module to container
                        $container->addModule($module, $options);
                        unset($this->config['containers'][$c][$module]);
                    }
                }
            }
        }
    }

    /**
     * Build Template Sources
     * @see Resource::buildSources()
     */
    public function buildSources()
    {

        // Template scripts
        if (!empty($this->config['scripts']) && is_array($this->config['scripts'])) {
            foreach ($this->config['scripts'] as $script) {
                $script = str_replace('{url}', Settings::getInstance()->URLS['URL'], $script);
                $check = $this->checkFile($script);
                if (empty($check)) {
                    $check = $this->skin->checkFile($script);
                }
                if (empty($check) && filter_var($script, FILTER_VALIDATE_URL)) {
                    $check = $script;
                }
                if (!empty($check)) {
                    $this->skin->addJavascript($check, 'page');
                }
            }
        }

        // Template styles
        if (!empty($this->config['styles']) && is_array($this->config['styles'])) {
            foreach ($this->config['styles'] as $style) {
                $check = $this->checkFile($style);
                if (empty($check)) {
                    $check = $this->skin->checkFile($style);
                }
                if (!empty($check)) {
                    $this->skin->addStylesheet($style, 'page');
                }
            }
        }

        // Template JavaScript
        $javascript = $this->checkFile($this->fileJavascript);
        if (!empty($javascript)) {
            $this->skin->addJavaScript($javascript, 'page');
        }

        // Template StyleSheet
        $stylesheet = $this->checkFile($this->fileStylesheet);
        if (!empty($stylesheet)) {
            $this->skin->addStylesheet($stylesheet);
        }
    }

    /**
     * Load Configuration
     * @return array|null
     * @throws Exception If configuration could not be loaded
     */
    protected function loadConfig()
    {
        // Locate Config File
        $config = $this->checkFile($this->fileConfig);
        if (!empty($config)) {
            // Load File Contents
            if (($json = file_get_contents($config)) !== false) {
                // Parse as JSON
                if (($config = json_decode($json, true)) !== false) {
                    if (!empty($config)) {
                        $this->config($config);
                        return $config;
                    }
                }
            }
        }
        // Error Loading Configuration
        throw new Exception('Template error: configuration could not be loaded - ' . $this->json_last_error_msg());
    }

    /**
     * Display Template Picker Form
     * @param PageInterface $page
     * @param string $pageTemplate
     * @param array $pageVariables
     * @return void
     * @deprecated use EditorInterface::displayForm
     */
    public static function displayForm(PageInterface $page, $pageTemplate = false, $pageVariables = array())
    {
        Container::getInstance()->make(EditorInterface::class, ['page' => $page])
            ->displayForm($pageTemplate, $pageVariables);
    }

    /**
     * Add required javascript to page
     * @param PageInterface $page
     * @return void
     * @deprecated use EditorInterface::requireJavascript
     */
    protected static function requireJavascript(PageInterface $page)
    {
        Container::getInstance()->make(EditorInterface::class, ['page' => $page])
            ->requireJavascript($page);
    }

    /**
     * Parse module options
     * @param array $options
     * @param PageInterface $page
     * @return array
     */
    protected function parseModuleOptions($options, PageInterface $page = null)
    {
        foreach ($options as $opt => $value) {
            if (is_array($value)) {
                $options[$opt] = $this->parseModuleOptions($value, $page);
            } elseif (is_string($value)) {
                $options[$opt] = $this->parseModuleOption($value, $page);
            }
        }
        return $options;
    }

    /**
     * Match dynamic options
     * @param string $value
     * @param PageInterface $page
     * @return mixed
     */
    protected function parseModuleOption($value, PageInterface $page = null)
    {

        // Match dynamic options
        preg_match('/\{(\!?info|\!?variable|\!?module|\!?request)\.([A-Za-z\-\_\.]+)\}/', $value, $match);
        if (!empty($match)) {
            // {!inverse} match
            $inverse = (substr($match[1], 0, 1) === '!');
            if (!empty($inverse)) {
                $match[1] = substr($match[1], 1);
            }

            // Page Information
            if ($page && $match[1] === 'info') {
                $value = $page->info($match[2]);

            // Page Variable
            } else if ($page && $match[1] === 'variable') {
                $value = $page->variable($match[2]);

            // $_REQUEST variable
            } else if ($match[1] === 'request') {
                $value = $_REQUEST[$match[2]];

            // Installed module
            } else if ($match[1] === 'module') {
                $value = $this->settings['MODULES'][$match[2]];
            }

            // NULL means no
            if (is_null($value)) {
                $value = false;
            }

            // Yes means no
            if ($inverse) {
                $value = !$value;
            }
        }

        // Return value
        return $value;
    }

    /**
     * @link http://php.net/manual/en/function.json-last-error-msg.php#113243
     * @return string
     */
    protected function json_last_error_msg()
    {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    }

}
