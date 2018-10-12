<?php

namespace REW\Backend\Controller\Navigation;

use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Navigation\ContentNavigation;
use REW\Backend\Navigation\CrmNavigation;
use REW\Backend\Navigation\ListingsNavigation;
use REW\Backend\Navigation\PeopleNavigation;
use REW\Backend\Navigation\SettingsNavigation;
use REW\Backend\Asset\Interfaces\LoaderInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Backend\Navigation\Interfaces\NavigationInterface;

/**
 * SidebarerController
 * @package REW\Backend\Controller\Navigation
 */
class SidebarController extends AbstractController
{

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var ContentNavigation
     */
    protected $contentNavigation;

    /**
     * @var CrmNavigation
     */
    protected $crmNavigation;

    /**
     * @var ListingsNavigation
     */
    protected $listingsNavigation;

    /**
     * @var PeopleNavigation
     */
    protected $peopleNavigation;

    /**
     * @var SettingsNavigation
     */
    protected $settingsNavigation;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @param AuthInterface $auth
     * @param HooksInterface $hooks
     * @param SettingsInterface $settings
     * @param ContentNavigation $contentNavigation
     * @param CrmNavigation $crmNavigation
     * @param ListingsNavigation $listingsNavigation
     * @param PeopleNavigation $peopleNavigation
     * @param SettingsNavigation $settingsNavigation
     * @param LoaderInterface $loader
     * @param FactoryInterface $view
     */
    public function __construct(
        AuthInterface $auth,
        HooksInterface $hooks,
        SettingsInterface $settings,
        ContentNavigation $contentNavigation,
        CrmNavigation $crmNavigation,
        ListingsNavigation $listingsNavigation,
        PeopleNavigation $peopleNavigation,
        SettingsNavigation $settingsNavigation,
        LoaderInterface $loader,
        FactoryInterface $view
    ) {
        $this->auth               = $auth;
        $this->hooks              = $hooks;
        $this->settings           = $settings;
        $this->contentNavigation  = $contentNavigation;
        $this->crmNavigation      = $crmNavigation;
        $this->listingsNavigation = $listingsNavigation;
        $this->peopleNavigation   = $peopleNavigation;
        $this->settingsNavigation = $settingsNavigation;
        $this->loader             = $loader;
        $this->view               = $view;
    }

    /**
     * Render Navigation Sidebar
     */
    public function __invoke()
    {

        // Get Application & Page
        list($app, $app_page) = explode('/', $_GET['page'], 2);
        if ($app == 'email') {
            $app = (isset($_GET['type']) && in_array($_GET['type'], ['leads','agents','associates','lenders'])) ? $_GET['type'] : 'leads' ;
            $app_page = 'email';
        }

        $navigationManager = $this->getNavigationManager($app, $app_page);
        if (isset($navigationManager)) {
            $navigation = $navigationManager->getNavLinks($app, $app_page);
            $add_links = $navigationManager->getAddLinks();
            $nav_name = $navigationManager->getNavName();

            // Execute sidebar navigation hooks
            $navigation = $this->hooks->hook(HooksInterface::HOOK_BACKEND_APP_NAV_CONTENT)->run($navigation ?: [], $nav_name) ?: $navigation;
            $add_links = $this->hooks->hook(HooksInterface::HOOK_BACKEND_APP_NAV_ADD_LINKS)->run($add_links ?: [], $nav_name) ?: $add_links;

            // Load route's template file
            $template = $this->loader->getTemplateFile("navigation/sidebar");

            // Render template file
            echo $this->view->render($template, [
                'navigation' => $navigation ?: [],
                'addLinks' => $add_links ?: []
            ]);
        }
    }

    /**
     * Get Navigation Manager from Page
     * @param string $app
     * param string $app_page
     * @return NavigationInterface|NULL
     */
    public function getNavigationManager($app, $app_page)
    {

        // Find Navigation Manager
        if (in_array($app, ['agents', 'associates', 'lenders', 'teams', 'reports'])) {
            return $this->peopleNavigation;
        } else if (in_array($app, ['blog', 'cms']) ||
            ($app == 'idx' && in_array($app_page, ['snippets', 'snippets/add', 'snippets/edit'])) ||
            ($app == 'bdx' && $app_page == 'snippets')) {
            return $this->contentNavigation;
        } else if (in_array($app, ['calendar', 'leads'])) {
            return $this->crmNavigation;
        } else if (in_array($app, ['idx', 'listings', 'developments']) && $app_page != 'snippets') {
            return $this->listingsNavigation;
        } else if (in_array($app, ['partners', 'settings'])) {
            return $this->settingsNavigation;
        }
        return null;
    }
}
