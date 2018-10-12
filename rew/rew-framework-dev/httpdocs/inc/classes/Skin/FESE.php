<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\ContainerInterface;

/**
 * "Fredrik Eklund Special Edition"
 * @package REW
 * @subpackage Skin
 */
class Skin_FESE extends Skin_BREW
{

    /**
     * List of features this skin uses
     * @var array of strings
     */
    protected static $features = [
        Skin::INCLUDE_NEWEST_LISTINGS_FIRST, // Allow sorting IDX results by "Price, Highest First"
        Skin::COMMUNITY_DESCRIPTION_NO_LIMIT, // Disable character limit for community descriptions
        Skin::COMMUNITY_DISABLE_ANCHOR_LINKS, // Disable "Anchor Links" for featured communities
        Skin::REGISTRATION_ON_MORE_PICS, // Enable "Registration To View All Photos" feature
        Skin::LINK_COMMUNITY_TO_PAGE,
        Skin::HIDE_SEARCH_TAGS,
        Skin::COMMUNITY_TAGS,
        Skin::REW_DEVELOPMENTS,
        Skin::SUBDOMAIN_FEATURE_IMAGE,
        Skin::SAVED_SEARCHES_RESPONSIVE,
        Skin::AGENT_EDIT_NETWORKS,
    ];

    /**
     * Directory
     * @var string
     */
    public static $directory = 'fese';

    /**
     * Name
     * @var string
     */
    protected $name = 'Fredrik Eklund Special Edition';

    /**
     * Skin Template
     * @var string
     */
    protected $template = 'basic';

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

        // Require BREW stylesheet
        $this->addStylesheet('css/lib/brew.min.css');

        // Include skin/scheme configuration
        $this->addStylesheet('css/config.less');
        $this->addStylesheet('schemes/' . $scheme . '/config.less');

        // Include skin/scheme stylesheets
        $this->addStylesheet('css/skin.less');
        $this->addStylesheet('css/compliance.less');
        $this->addStylesheet('schemes/' . $scheme . '/scheme.less');

        // Include required scripts
        $this->addJavascript('js/utils.js', 'static');
        $this->addJavascript('js/skin.js', 'static');
        $this->addJavascript('js/app.js', 'static');

        // Defer loading of icon fonts
        $this->addStylesheet('lib/font-awesome-4.7.0/css/font-awesome.min.css', 'defer');
        $this->addStylesheet('lib/ionicons-2.0.1/css/ionicons.min.css', 'defer');
    }

    /**
     * @see Skin::getTemplate()
     */
    public function getTemplate(PageInterface $page = null)
    {

        // Use expected template for IDX details & search result pages
        if ($page && in_array($page->info('app'), array('idx', 'idx-map'))) {
            if (in_array($page->info('name'), array('details', 'map', 'birdseye', 'streetview', 'local'))) {
                $this->template = 'idx/details';
            } elseif (in_array($page->info('name'), array('search', 'search_map'))) {
                $this->template = 'idx/results';
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

        // IDX quick search module
        $page = $this->getPage();
        $template = $page->getTemplate();
        if ($template && $template->getName() === 'basic' // Basic only
            && in_array($page->info('app'), ['cms', 'blog'])
            && !isset($_REQUEST['popup'])   // Exclude on modal pages
        ) {
            if ($page->info('subdomain-site') && $page->info('name') === 'homepage' && $page->info('feature_image')) {
                $page->info('class', $page->info('class') . ' ' . 'subdomain-cover');
                $page->container('cover-quick-search')->addModule('feature-slides', array(
                    'slides' => array(
                        array(
                            'image'=> URL_FEATURED_IMAGES . $page->info('feature_image'),
                            'showSearchForm'=> 1,
                            'posVertical' => 'B',
                            'posHorizontal' => 'M'
                        )
                    )
                ));
            } else if (empty($_REQUEST['snippet']) // Exclude on snippet pages
                || $page->moduleLoaded('agents')
                || $page->moduleLoaded('property-valuation')
                || $page->moduleLoaded('idx-featured-search')
            ) {
                $page->container('quick-search')->addModule('idx-search', [
                    'showFeeds' => true,
                    'advanced' > false,
                    'prepend' => true
                ]);
            }
        }
    }

    /**
     * Gets the file for the backend WYSIWYG editor to consume. Nom nom.
     * @return string
     */
    public static function getWYSIWYGHelperCSSFile()
    {
        $path = Settings::getInstance()->DIRS['SKINS'] . static::getDirectory();
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
        return $path . '/css/lib/tinymce.css';
    }

    /**
     * Get namespace to use for object oriented modules. This is necessary to make inheritance without renaming
     * possible.
     * @return string
     */
    public function getModuleNamespace()
    {
        return parent::getModuleNamespace() . 'FESE\\';
    }
}
