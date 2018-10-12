<?php

namespace REW\Providers;

use REW\Provider;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\DatabaseInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;

class IDXProvider extends Provider
{

    /**
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();
        $factory = $container->get(IDXFactoryInterface::class);
        $container['idx'] = $idx = $factory->getIdx();
        $container->set(IDXInterface::class, $idx);
        $container['dbidx'] = $dbIdx = $factory->getDatabase();
        $container->set(DatabaseInterface::class, $dbIdx);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['idx', 'dbidx', IDXInterface::class, DatabaseInterface::class];
    }
}
