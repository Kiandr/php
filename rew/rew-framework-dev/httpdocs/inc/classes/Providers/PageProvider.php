<?php

namespace REW\Providers;

use Page;
use REW\Provider;
use REW\Core\Interfaces\Page\BackendInterface;
use REW\Core\Interfaces\PageInterface;

class PageProvider extends Provider
{

    /**
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();
        $skin = $container['skin'];
        $backendSkin = $container['skin-backend'];

        $container['page'] = $container['page-frontend'] = $page = $container->make(Page::class, ['skin' => $skin]);
        $container->set(PageInterface::class, $page);

        $container['page-backend'] = $pageBackend = $container->make(Page::class, ['skin' => $backendSkin]);
        $container->set(BackendInterface::class, $pageBackend);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['page', 'page-frontend', 'page-backend', PageInterface::class, BackendInterface::class];
    }
}
