<?php

namespace REW\Core\Interfaces;

interface RouterInterface
{
    /**
     * @param RouteInterface $route
     */
    public function __construct(RouteInterface $route);

    /**
     * @param RouteInterface $route
     */
    public function setRoute(RouteInterface $route);

    /**
     * @return RouteInterface
     */
    public function getRoute();
}
