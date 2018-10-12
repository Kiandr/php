<?php

use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Page\BackendInterface;

/**
 * Backend Skin (this one is not like the others)
 * @package REW
 * @subpackage Skin
 */
class Skin_Backend extends Skin
{

    /**
     * Name
     * @var string
     */
    protected $name = 'Backend';

    /**
     * Body class
     * @var string
     */
    protected $bodyClass;

    /**
     * Create Skin
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     */
    public function __construct(ContainerInterface $container = null, SettingsInterface $settings = null)
    {
        if ($container === null) {
            $container = Container::getInstance();
        }
        if ($settings === null) {
            $settings = $container->get(SettingsInterface::class);
        }

        $container->callConstructor($this, parent::class, ['directory' => null]);
        $this->setPath($settings->DIRS['BACKEND']);

        // REW JS Variables
        $skinClass = Skin::getClass();
        $this->addJavascript('window.__BACKEND__ = ' . json_encode([
            'backend_url' => $settings->URLS['URL_BACKEND'],
            'tinymce_styles' => $skinClass::getWYSIWYGHelperCSSFile()
        ]) . ';');

        // No Skin Controller
        $this->controller = false;

        // Get current route w/o trailing slash
        $routeName = rtrim($_GET['page'], '/');

        // Use "blank page" template when not logged in (or viewing from ?popup URL)
        if (in_array($routeName, array('login', 'reset', 'remind', 'download')) || isset($_GET['popup']) || isset($_POST['popup'])) {
            $this->template = 'blank.tpl';
        } else {
            $this->template = 'index.tpl';
        }
    }

    /**
     * Display Skin
     * @param bool $return Return Output
     */
    public function display($return = false)
    {

        // Set Page
        $this->setPage($page = $this->container->get(BackendInterface::class));
        $body_class = $this->getBodyClass();

        // Start Output Buffer
        if ($return) {
            ob_start();
        }

        // Load Controller
        $php = $this->checkFile($this->getController());
        if (!empty($php)) {
            include_once $php;
        }

        // Build Sources
        $this->buildSources();

        // Build Body Class
        $this->generateBodyClass($body_class);

        // Load Template
        $tpl = $this->checkFile($this->getTemplate());
        if (!empty($tpl)) {
            include_once $tpl;
        }

        // Return Output
        if ($return) {
            return ob_get_clean();
        }
    }

    /**
     * Generate Class String for <body> Tag
     *
     * @param string $body_class
     */
    protected function generateBodyClass(&$body_class = '')
    {

        // <body> Class
        $body_class = (!empty($body_class) ? $body_class . ' ' : '') . str_replace('/', '-', $_GET['page']);

        // Sidebar Hidden (Use Full Body)
        if ($_COOKIE['show_sidebar'] == 'no') {
            $body_class .=  ' full';
        }
    }

    /**
     * @inheritDoc
     */
    protected function addForcedVerification()
    {
    }

    /**
     * @inheritDoc
     */
    protected function addForcedRegistration(array $listing, $registration_required)
    {
    }

    /**
     * Set class to apply to body
     * @param string $bodyClass
     */
    public function setBodyClass($bodyClass)
    {
        $this->bodyClass = $bodyClass;
    }

    /**
     * Get class to apply to body
     * @return string
     */
    public function getBodyClass()
    {
        return $this->bodyClass;
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'Backend\\';
    }
}
