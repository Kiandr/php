<?php

return [
    REW\Core\Asset\Interfaces\LoaderInterface::class => function () {
        return new REW\Core\Asset\Loader(
            null,
            null,
            null
        );
    },
    REW\Core\Interfaces\AuthInterface::class => function (\REW\Session $session) {
        // Ensure session is started before getting Auth
        $session->startSession();
        return Auth::get();
    },
    REW\Core\Interfaces\Backend\CMSInterface::class => Backend_CMS::class,
    REW\Core\Interfaces\Backend\ContactSnippetsInterface::class => Backend_ContactSnippets::class,
    REW\Core\Interfaces\CacheInterface::class => Cache::class,
    REW\Core\Interfaces\EnvironmentInterface::class => Environment::class,
    REW\Core\Interfaces\Factories\DBFactoryInterface::class => DBFactory::class,
    REW\Core\Interfaces\Factories\IDXFactoryInterface::class => IDXFactory::class,
    REW\Core\Interfaces\Factories\SnippetFactoryInterface::class => SnippetFactory::class,
    REW\Core\Interfaces\FormatInterface::class => Format::class,
    REW\Core\Interfaces\HookInterface::class => Hook::class,
    REW\Core\Interfaces\Hook\CollectionInterface::class => Hook_Collection::class,
    REW\Core\Interfaces\HooksInterface::class => Hooks::class,
    REW\Core\Interfaces\Hooks\SkinInterface::class => Hooks_Skin::class,
    REW\Core\Interfaces\Http\HostInterface::class => Http_Host::class,
    REW\Core\Interfaces\IDX\ComplianceInterface::class => IDX_Compliance::class,
    REW\Core\Interfaces\InstallerInterface::class => Installer::class,
    REW\Core\Interfaces\LogInterface::class => Log::class,
    REW\Core\Interfaces\NamespaceContainerInterface::class => NamespaceContainer::class,
    REW\Core\Interfaces\ModuleInterface::class => Module::class,
    REW\Core\Interfaces\Page\ContainerInterface::class => Page_Container::class,
    REW\Core\Interfaces\Page\TemplateInterface::class => Page_Template::class,
    REW\Core\Interfaces\Page\Template\EditorInterface::class => Page_Template_Editor::class,
    REW\Core\Interfaces\SettingsFileMergerInterface::class => SettingsFileMerger::class,
    REW\Core\Interfaces\SettingsInterface::class => Settings::class,
    REW\Core\Interfaces\Snippet\ResultInterface::class => SnippetResult::class,
    REW\Core\Interfaces\Util\CMSInterface::class => Util_CMS::class,
    REW\Core\Interfaces\Util\IDXInterface::class => Util_IDX::class,
    REW\Core\Interfaces\Util\ModuleControllerInterface::class => Util_ModuleController::class,
    REW\Core\Interfaces\YamlParserInterface::class => YamlParser::class,
    REW\Backend\View\Interfaces\FactoryInterface::class => function ($c) {
        $factory = $c->make(REW\Backend\View\Factory::class);
        $factory->registerEngine('php', function () use ($c) {
            return $c->make(REW\Backend\View\Engine\PhpEngine::class);
        });
        return $factory;
    },
    REW\Backend\View\Interfaces\LoaderInterface::class => function ($c) {
        $loader = $c->make(REW\Backend\View\Loader::class);
        if ($views = realpath(__DIR__ . '/../httpdocs/backend/inc/tpl')) {
            $loader->registerNamespace('', $views);
        }
        if ($views = realpath(__DIR__ . '/../httpdocs/backend/assets/views')) {
            $loader->registerNamespace('backend', $views);
        }
        return $loader;
    },
    REW\View\Interfaces\FactoryInterface::class => function ($c) {
        return $c->get(REW\Backend\View\Interfaces\FactoryInterface::class);
    },
    REW\View\Interfaces\LoaderInterface::class => function ($c) {
        return $c->get(REW\Backend\View\Interfaces\LoaderInterface::class);
    },
    REW\Contracts\View\Factory::class => function ($c) {
        $factory = $c->get(REW\View\Factory::class);
        $factory->registerEngine('php', function () use ($c) {
            return $c->get(REW\Backend\View\Engine\PhpEngine::class);
        });
        return $factory;
    },
    REW\Contracts\View\Loader::class => function ($c) {
        return $c->get(REW\View\Interfaces\LoaderInterface::class);
    },
    REW\View\Engine\TwigEngine::class => REW\Core\View\Engine\TwigEngine::class,
    REW\Core\Interfaces\RouterInterface::class => REW\Core\Router::class,
    REW\Core\Interfaces\RouteInterface::class => function () {
        return new REW\Core\Route($_GET['app'] . '/' . ($_GET['load_page'] ?: $_GET['id']));
    },
    REW\Factory\Community\ResultModelFactoryInterface::class => REW\Factory\Community\Result\ResultModelFactory::class,
    REW\Factory\Community\RequestModelFactoryInterface::class => \REW\Factory\Community\Request\RequestModelFactory::class,
    REW\Datastore\Community\SearchDatastoreInterface::class => \REW\Datastore\Community\SearchDatastore::class,
    REW\Factory\Idx\Search\FieldFactoryInterface::class => REW\Factory\Idx\Search\Field\FieldFactory::class,
    REW\Datastore\Listing\SearchFieldDatastoreInterface::class => REW\Datastore\Listing\SearchFieldDatastore::class,
    REW\Datastore\Listing\SavedSearchDatastoreInterface::class => REW\Datastore\Listing\SavedSearchDatastore::class,
    REW\Factory\Idx\SavedSearch\Request\RequestInterface::class => REW\Factory\Idx\SavedSearch\Request\RequestFactory::class,
    REW\Factory\Idx\SavedSearch\Result\ResultInterface::class => REW\Factory\Idx\SavedSearch\Result\ResultFactory::class,
    REW\Model\Idx\SavedSearch\Request\RequestInterface::class => REW\Model\Idx\SavedSearch\Request\RequestModel::class,
    REW\Model\Idx\SavedSearch\Result\ResultInterface::class => REW\Model\Idx\SavedSearch\Result\ResultModel::class,
    REW\Factory\Idx\Search\FeedInfoFactoryInterface::class => REW\Factory\Idx\Search\FeedInfo\FeedInfoFactory::class,
    REW\Factory\Idx\Favorite\FavoriteListingFactoryInterface::class => REW\Factory\Idx\Favorite\Listing\FavoriteListingFactory::class,
    REW\Factory\User\Token\ResultInterface::class => REW\Factory\User\Token\ResultFactory::class,
    \REW\Core\Interfaces\UserInterface::class => User_Session::class,
    \REW\Factory\Idx\Search\PanelRequestFactoryInterface::class => \REW\Factory\Idx\Search\Panel\PanelRequestFactory::class,
    \REW\Factory\Idx\Search\PanelResultFactoryInterface::class => \REW\Factory\Idx\Search\Panel\PanelResultFactory::class,
    \REW\Model\Idx\Search\PanelRequestInterface::class => \REW\Model\Idx\Search\Panel\PanelRequest::class,
    \REW\Model\Idx\Search\PanelResultInterface::class => \REW\Model\Idx\Search\Panel\PanelResult::class,
    \REW\Datastore\Listing\SearchPanelDatastoreInterface::class => \REW\Datastore\Listing\SearchPanelDatastore::class
];
