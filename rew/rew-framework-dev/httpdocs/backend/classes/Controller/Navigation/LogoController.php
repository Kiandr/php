<?php

namespace REW\Backend\Controller\Navigation;

use REW\Backend\Controller\AbstractController;
use REW\Backend\Navigation\ContentNavigation;
use REW\Backend\Navigation\CrmNavigation;
use REW\Backend\Navigation\ListingsNavigation;
use REW\Backend\Navigation\PeopleNavigation;
use REW\Backend\Navigation\SettingsNavigation;
use REW\Backend\Asset\Interfaces\LoaderInterface;
use REW\Backend\View\Interfaces\FactoryInterface;

/**
 * LogoController
 * @package REW\Backend\Controller\Navigation
 */
class LogoController extends AbstractController
{

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
     * @param ContentNavigation $contentNavigation
     * @param CrmNavigation $crmNavigation
     * @param ListingsNavigation $listingsNavigation
     * @param PeopleNavigation $peopleNavigation
     * @param SettingsNavigation $settingsNavigation
     * @param LoaderInterface $loader
     * @param FactoryInterface $view
     */
    public function __construct(
        ContentNavigation $contentNavigation,
        CrmNavigation $crmNavigation,
        ListingsNavigation $listingsNavigation,
        PeopleNavigation $peopleNavigation,
        SettingsNavigation $settingsNavigation,
        LoaderInterface $loader,
        FactoryInterface $view
    ) {
        $this->contentNavigation  = $contentNavigation;
        $this->crmNavigation      = $crmNavigation;
        $this->listingsNavigation = $listingsNavigation;
        $this->peopleNavigation   = $peopleNavigation;
        $this->settingsNavigation = $settingsNavigation;
        $this->loader             = $loader;
        $this->view               = $view;
    }

    /**
     * Creates Navigation Logo
     */
    public function __invoke()
    {

        // Get Application & Page
        list($app, $app_page, $app_section) = explode('/', $_GET['page'], 3);
        if ($app == 'email') {
            $app = (isset($_GET['type']) && in_array($_GET['type'], ['leads','agents','associates','lenders'])) ? $_GET['type'] : 'leads' ;
            $app_page = 'email';
        }

        if ($app == 'dashboard') {
            $enabled = true;
            $link = "/backend/";
            $title = 'Dashboard';
            $class = 'sprite sprite--small sprite--dashboard';
        } else if ($app == 'agents' || $app == 'associates' || $app == 'teams' || $app == 'lenders' || $app == 'reports') {
            $enabled = $this->peopleNavigation->isEnabled();
            $link = $this->peopleNavigation->getLandingLink();
            $title = 'Company';
            $class = 'sprite sprite--small sprite--people';
        } else if ($app == 'leads' || $app == 'calendar') {
            $enabled = $this->crmNavigation->isEnabled();
            $link = $this->crmNavigation->getLandingLink();
            $title = 'CRM';
            $class = 'sprite sprite--small sprite--crm';
        } else if ($app == 'cms' || $app == 'blog' || ($app == 'idx' && in_array($app_page, ['snippets', 'snippets/add', 'snippets/edit']))) {
            $enabled = $this->contentNavigation->isEnabled();
            $link = $this->contentNavigation->getLandingLink();
            $title = 'Content';
            $class = 'sprite sprite--small sprite--content';
        } else if ($app == 'listings' || $app == 'idx' || $app == 'developments') {
            $enabled = $this->listingsNavigation->isEnabled();
            $link = $this->listingsNavigation->getLandingLink();
            $title = 'Listings';
            $class = 'sprite sprite--small  sprite--listings';
        } else if (in_array($app, ['settings', 'partners'])) {
            $enabled = $this->settingsNavigation->isEnabled();
            $link = $this->settingsNavigation->getLandingLink();
            $title = 'Settings';
            $class = 'sprite sprite--small sprite--settings';
        }

        // Load route's template file
        $template = $this->loader->getTemplateFile("navigation/logo");

        // Render template file
        echo $this->view->render($template, [
            'enabled' => $enabled ?: false,
            'link' => $link ?: URL_BACKEND,
            'title' => $title ?: '',
            'class' => $class ?: ''
        ]);
    }
}
