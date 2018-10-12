<?php

namespace REW\Providers;

use User_Session;
use REW\Provider;
use REW\Core\Interfaces\User\SessionInterface;

class UserSessionProvider extends Provider
{

    /**
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();
        $container['session'] = $user = User_Session::get();
        $container->set(SessionInterface::class, $user);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['session', SessionInterface::class];
    }
}
