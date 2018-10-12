<?php

namespace REW\Backend;

use REW\Backend\Interfaces\RouteInterface;
use REW\Backend\Interfaces\RouterInterface;

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
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }
}
