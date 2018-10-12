<?php

namespace REW\Theme\Enterprise;

use REW\Theme\Boilerplate\Theme as BoilerplateTheme;

/**
 * @package REW\Theme\Enterprise
 */
class Theme extends BoilerplateTheme
{

    /**
     * @var string
     */
    public static $directory = 'ce';

    /**
     * @var string[]
     */
    protected static $features = [
        BoilerplateTheme::INCLUDE_NEWEST_LISTINGS_FIRST, // Allow sorting IDX results by "Price, Highest First"
        BoilerplateTheme::COMMUNITY_DESCRIPTION_NO_LIMIT, // Disable character limit for community descriptions
        BoilerplateTheme::COMMUNITY_DISABLE_ANCHOR_LINKS, // Disable "Anchor Links" for featured communities
        BoilerplateTheme::REGISTRATION_ON_MORE_PICS, // Enable "Registration To View All Photos" feature
        BoilerplateTheme::TESTIMONIAL_ASSIGN_AGENT, //  Allow client testimonials to be assigned agents
        BoilerplateTheme::LINK_COMMUNITY_TO_PAGE,
        BoilerplateTheme::COMMUNITY_TAGS,
        BoilerplateTheme::AGENT_EDIT_NETWORKS,
        BoilerplateTheme::SAVED_SEARCHES_RESPONSIVE
    ];

    /**
     * @var string
     */
    protected $template = 'basic';

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {

        // Include BREW CSS framework
        $nodeModules = sprintf('%s/../node_modules/', __DIR__);
        $this->addStylesheet(sprintf('%s/brew/dist/css/brew.css', $nodeModules));

        // Include configuration files
        $this->addStylesheet('css/config.less');
        $this->addStylesheet(sprintf('schemes/%s/config.less', $this->getScheme()));

        // Include theme's LESS stylesheets
        $this->addStylesheet('css/skin.less');
        $this->addStylesheet('css/compliance.less');

        // Include required scripts
        $this->addJavascript('js/config.js', 'dynamic');
        $this->addJavascript('js/skin.js', 'static');
        $this->addJavascript('js/app.js', 'static');

        // Defer loading of fonts
        $this->addStylesheet('lib/font-awesome-4.7.0/css/font-awesome.min.css', 'defer');
        $this->addStylesheet('lib/ionicons-2.0.1/css/ionicons.min.css', 'defer');
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplate()
    {
        $page = $this->getPage();
        if ($page && in_array($page->info('app'), array('idx', 'idx-map'))) {
            if ($page->info('name') === 'details') {
                $this->template = 'idx/details';
            } elseif (in_array($page->info('name'), array('search', 'search_map'))) {
                $this->template = 'idx/results';
            }
        }
        return parent::getTemplate();
    }

    /**
     * {@inheritDoc}
     */
    protected function extraSources()
    {
        parent::extraSources();
        $page = $this->getPage();
        $template = $page->getTemplate();
        if ($template && $template->getName() === 'cover') {
            $background = $page->variable('background');
            $this->addBackgroundDependencies($background);
        }

        // Include scheme stylesheet file
        $this->addStylesheet(sprintf('schemes/%s/scheme.less', $this->getScheme()));

        // Include custom stylesheet file
        $this->addStylesheet('css/custom.less');
    }

    /**
     * @param string $enhancedFeatureType
     */
    public function addBackgroundDependencies($enhancedFeatureType)
    {
        if ($enhancedFeatureType === 'video') {
            $this->addJavascript('node_modules/jQuery.YoutubeBackground/src/jquery.youtubebackground.js', 'static');
        }

        if ($enhancedFeatureType === 'pano') {
            $this->addJavascript('node_modules/jquery-pano/jquery.pano.js', 'static');
        }

        if ($enhancedFeatureType === '360') {
            $this->addJavascript('node_modules/pannellum/src/js/libpannellum.js', 'dynamic');
            $this->addJavascript('node_modules/pannellum/src/js/RequestAnimationFrame.js', 'dynamic');
            $this->addJavascript('node_modules/pannellum/src/js/pannellum.js', 'dynamic');
            $this->addStylesheet('node_modules/pannellum/src/css/pannellum.css', 'static');
        }
    }
}
