<?php

namespace REW\Backend;

use REW\Backend\Asset\Interfaces\LoaderInterface;
use REW\Backend\Controller\BackendPageController;
use REW\Backend\Exceptions\InvalidActionException;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\UserErrorException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\Interfaces\RouterInterface;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\HooksInterface;

class Application
{

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var string
     */
    const NOT_FOUND_ROUTE = '404';

    /**
     * @var string
     */
    const UNAUTHORIZED_ROUTE = 'unauthorized';

    /**
     * @var string
     */
    const USER_ERROR_ROUTE = 'user_error';

    /**
     * @var string
     */
    const SYSTEM_ERROR_ROUTE = 'system_error';

    /**
     * @var string
     */
    const INVALID_ACTION_ROUTE = 'invalid_action';

    /**
     * @param HooksInterface $hooks
     * @param LoaderInterface $loader
     * @param RouterInterface $router
     * @param NoticesCollectionInterface $notices
     */
    public function __construct(
        HooksInterface $hooks,
        LoaderInterface $loader,
        RouterInterface $router,
        NoticesCollectionInterface $notices
    ) {
        $this->hooks = $hooks;
        $this->loader = $loader;
        $this->router = $router;
        $this->notices = $notices;
    }

    /**
     * @param string $route
     */
    public function setCurrentRoute($route)
    {
        $this->router->setRoute(
            new Route($route)
        );
    }

    /**
     * @param BackendInterface $page
     * @return void
     */
    public function run(BackendInterface $page)
    {
        $routePath = $this->router->getRoute()->getPath();
        $this->loadRouteController($routePath);
        $this->loadRouteAssets($page, $routePath);
    }

    /**
     * @param $routePath
     */
    protected function loadRouteController($routePath)
    {
        try {
            // Execute hook used to allow over-riding route controllers
            if ($this->hooks->hook(HooksInterface::HOOK_BACKEND_APP_RUN)->run(false, $routePath)) {
                return;
            }

            // Use invokable route controller instead of controller/template files
            if ($controllerClass = $this->resolveRouteController($routePath)) {
                $controller = \Container::getInstance()->make($controllerClass);
                call_user_func_array($controller, []);
                return;
            } else {
                // This should never ever happen
                throw new SystemErrorException;
            }

        // Page not found exception (404)
        } catch (PageNotFoundException $e) {
            header('HTTP/1.1 404 NOT FOUND');
            $this->handleError(self::NOT_FOUND_ROUTE, $e);

        // Unauthorized page
        } catch (UnauthorizedPageException $e) {
            $this->handleError(self::UNAUTHORIZED_ROUTE, $e);

        // Invalid action
        } catch (InvalidActionException $e) {
            $this->handleError(self::INVALID_ACTION_ROUTE, $e);

        // User error occurred
        } catch (UserErrorException $e) {
            $this->handleError(self::USER_ERROR_ROUTE, $e);

        // System error occurred
        } catch (SystemErrorException $e) {
            $this->handleError(self::SYSTEM_ERROR_ROUTE, $e);
        }
    }

    /**
     * @param string $errorRoute
     * @param \Exception $e
     */
    protected function handleError($errorRoute, \Exception $e)
    {
        if ($controller = $this->loader->getControllerFile($errorRoute)) {
            require_once $controller;
        }
        if ($template = $this->loader->getTemplateFile($errorRoute)) {
            require_once $template;
        }
    }

    /**
     * @param BackendInterface $page
     * @param $routePath
     */
    protected function loadRouteAssets(BackendInterface $page, $routePath)
    {

        // CSS stylesheets
        $styles = ['app'];

        // JS assets to be included
        $scripts = ['manifest', 'vendor', 'bundle'];

        // Route specific assets
        $scripts[] = $styles[] = sprintf('pages/%s', $routePath);
        $scripts[] = $styles[] = sprintf('pages/%s/default', $routePath);

        // Include module assets
        foreach ($page->fetchContainers() as $container) {
            foreach ($container->fetchModules() as $module) {
                $scripts[] = $styles[] = sprintf('modules/%s', $module->getId());
            }
        }

        // Add verified scripts to page
        foreach ($scripts as $asset) {
            if ($script = $this->loader->getJavascriptFile($asset)) {
                $page->addJavascript($script, 'external');
            }
        }

        // Add verified styles to page
        foreach ($styles as $asset) {
            if ($style = $this->loader->getStylesheetFile($asset)) {
                $page->addStylesheet($style, 'external');
            }
        }
    }

    /**
     * Get route's controller class
     * @param string $routePath
     * @return string
     */
    protected function resolveRouteController($routePath)
    {
        $controllerName = str_replace('/', ' ', $routePath);
        $controllerName = str_replace(' ', '\\', ucwords($controllerName));
        $controllerName = str_replace(' ', '', ucwords(str_replace('-', ' ', $controllerName)));
        $controllerClasses = ['%s\Controller\%sController', '%s\Controller\%s\IndexController'];
        foreach ($controllerClasses as $controllerClass) {
            $controllerClass = sprintf($controllerClass, __NAMESPACE__, $controllerName);
            if (class_exists($controllerClass)) {
                return $controllerClass;
            }
        }
        return BackendPageController::class;
    }

    /**
     * Returns The Notices Collection
     * @return NoticesCollectionInterface
     */
    public function getNotices()
    {
        return $this->notices;
    }
}
