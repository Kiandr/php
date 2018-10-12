<?php

return [
    REW\Backend\Interfaces\NoticesCollectionInterface::class => REW\Backend\NoticesCollection::class,
    REW\Backend\Interfaces\RouteInterface::class => function () {
        return new REW\Backend\Route($_GET['page']);
    },
    REW\Backend\Interfaces\RouterInterface::class => REW\Backend\Router::class,
    REW\Backend\Interfaces\SessionInterface::class => REW\Backend\Session::class,
    /**
     * REW\Backend\Asset\Loader
     */
    REW\Backend\Asset\Interfaces\LoaderInterface::class => function () {
        $manifest = '%s/build/%s/assets.json';
        $basePath = realpath(__DIR__ . '/..');
        $scripts = sprintf($manifest, $basePath, 'js');
        $styles = sprintf($manifest, $basePath, 'css');
        return new REW\Backend\Asset\Loader(
            $basePath,
            new REW\Backend\Asset\Manifest\ManifestFile($styles),
            new REW\Backend\Asset\Manifest\ManifestFile($scripts)
        );
    },
    REW\Backend\CMS\Interfaces\SubdomainInterface::class => REW\Backend\CMS\Subdomain::class,
    REW\Backend\CMS\Interfaces\SubdomainFactoryInterface::class => REW\Backend\CMS\SubdomainFactory::class,
    REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface::class => REW\Backend\Leads\CustomFieldFactory::class

];
