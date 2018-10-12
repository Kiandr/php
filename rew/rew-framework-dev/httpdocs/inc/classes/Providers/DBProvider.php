<?php

namespace REW\Providers;

use REW\Provider;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;

class DBProvider extends Provider
{

    /**
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();

        $container['db'] = $db = function () use ($container) {
            $factory = $container->get(DBFactoryInterface::class);

            return $factory->get();
        };
        $container->set(DBInterface::class, $db);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['db', DBInterface::class];
    }
}
