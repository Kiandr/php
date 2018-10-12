<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ContainerInterface;

/**
 * LEC-2013 Skin
 * @package REW
 * @subpackage Skin
 */
class Skin_LEC2013 extends Skin_BREW
{

    /**
     * List of features that skin uses (used to define whether feature or feature configuration should display)
     * @var array of strings
     */
    protected static $features = array(
        Skin::INCLUDE_NEWEST_LISTINGS_FIRST,
        Skin::LINK_COMMUNITY_TO_PAGE,
        Skin::SUBDOMAIN_FEATURE_IMAGE,
    );

    /**
     * Directory
     * @var string
     */
    public static $directory = 'lec-2013';

    /**
     * Name
     * @var string
     */
    protected $name = 'LEC 2013';

    /**
     * Skin Template
     * @var string
     */
    protected $template = '2col';

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
        $this->addStylesheet('css/custom.less');

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

        // CMS Homepage
        if (Http_Uri::getUri() === '/' && $this->settings['SETTINGS']['agent'] === 1) {
            $this->template = 'homepage';

        // Blog Template (Use 2col)
        } else if ($page && $page->info('app') == 'blog') {
            $this->template = '2col';

        // Directory Template (Use 2col)
        } else if ($page && $page->info('app') == 'directory') {
            $this->template = '2col';

        // IDX Map Search
        } else if ($page && $page->info('app') == 'idx-map') {
            $this->template = '2col';

        // IDX Templates
        } else if ($page && $page->info('app') == 'idx') {
            // IDX Search Results
            if ($page && $page->info('name') == 'search') {
                $this->template = 'idx/search';

            // IDX Details Page
            } else if (in_array($page && $page->info('name'), array('details', 'map', 'birdseye', 'streetview', 'local'))) {
                $this->template = 'idx/detail';
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

        // Include Scheme's Styles
        $this->addStylesheet('schemes/' . $this->getScheme() . '/scheme.less');

        // Run parent
        parent::extraSources();
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'LEC2013\\';
    }
}
