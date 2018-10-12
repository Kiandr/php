<?php

namespace REW\Backend;

use REW\Core\Interfaces\BootableInterface;
use REW\Core\Interfaces\ContainerInterface;

class Installer implements BootableInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Installer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     */
    public function boot()
    {
        // Register backend interface classes
        $bindings = require __DIR__ . '/../config/bindings.php';
        if (is_array($bindings)) {
            foreach ($bindings as $abstract => $concrete) {
                $this->container->set($abstract, $concrete);
            }
        }
    }
}
