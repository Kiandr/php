<?php

namespace REW\Core;

use REW\Core\Interfaces\RouteInterface;
use REW\Core\Interfaces\RouterInterface;

class Router implements RouterInterface
{

    /**
     * @var Route
     */
    protected $route;

    /**
     * @param RouteInterface $route
     */
    public function __construct(RouteInterface $route)
    {
        $this->setRoute($route);
    }

    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
    }

    /**
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }
}
