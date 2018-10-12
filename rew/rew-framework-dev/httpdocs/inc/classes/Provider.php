<?php

namespace REW;

use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\ProviderInterface;

abstract class Provider implements ProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Provider constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return void
     */
    abstract public function register();

    /**
     * @return void
     */
    public function boot()
    {
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
