<?php

namespace REW\Providers;

use Auth;
use REW\Provider;
use REW\Core\Interfaces\AuthInterface;

class AuthProvider extends Provider
{

    /**
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();
        $container['auth'] = $auth = Auth::get();
        $container->set(AuthInterface::class, $auth);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['auth', AuthInterface::class];
    }
}
