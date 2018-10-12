<?php

namespace REW\Core;

use REW\Core\Controller\FrontendPageController;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\RouterInterface;
use REW\Core\Interfaces\PageInterface;
use \Exception;

class Application
{

    /**
     * Class name of default controller to invoke
     * Used in the event that none were resolved
     * @var string
     */
    const DEFAULT_CONTROLLER_CLASS = FrontendPageController::class;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param ContainerInterface $container
     * @param RouterInterface $router
     */
    public function __construct(
        ContainerInterface $container,
        RouterInterface $router
    ) {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * @param PageInterface $page
     * @return void
     */
    public function run(PageInterface $page)
    {
        ob_start();
        $routePath = $this->router->getRoute()->getPath();
        $routeController = $this->getRouteController($page, $routePath) ?: static::DEFAULT_CONTROLLER_CLASS;
        $this->loadRouteController($routeController);
        $output = ob_get_clean();
        $page->info('content', $output);
        $page->display();
    }

    /**
     * Get route's controller class
     * @param PageInterface $page
     * @param string $routePath
     * @return string
     */
    public function getRouteController(PageInterface $page, $routePath)
    {
        $skin = $page->getSkin();
        if (method_exists($skin, 'getThemeNamespaces')) {
            $controllerName = str_replace('/', ' ', $routePath);
            $controllerName = str_replace(' ', '\\', ucwords($controllerName));
            $controllerName = str_replace(' ', '', ucwords(str_replace('-', ' ', str_replace('_', '-', $controllerName))));
            $controllerClasses = ['%s\Controller\%sController', '%s\Controller\%s\IndexController'];
            foreach ($skin->getThemeNamespaces() as $ns) {
                foreach ($controllerClasses as $controllerClass) {
                    $controllerClass = sprintf($controllerClass, $ns, $controllerName);
                    if (class_exists($controllerClass)) {
                        return $controllerClass;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $routeController
     * @throws Exception If error occurred
     */
    protected function loadRouteController($controllerClass)
    {
        try {

            // Use invokable route controller first, then FrontendPageController
            $controller = $this->container->make($controllerClass);
            call_user_func_array($controller, []);
            return;

        // Unauthorized page
        } catch (Exception $e) {
            // @todo: exception handling
            throw $e;
        }
    }
}
