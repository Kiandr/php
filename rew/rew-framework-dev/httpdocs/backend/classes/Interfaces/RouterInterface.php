<?php

namespace REW\Backend\Interfaces;

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
     * @return Route
     */
    public function getRoute();
}
