<?php

namespace REW\Providers;

use REW\Core\Interfaces\ProviderInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

/**
 * Class HttpRequestProvider
 * @package REW
 * @subpackage Providers
 *
 * This provider provides PSR7 RequestInterface and ServerRequestInterface. If run in CLI, it uses
 * settings['site']['default-hostname'] to determine the correct hostname, since the settings are built without
 * the assistance of request vars
 */
class HttpRequestProvider implements ProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * HttpRequestProvider constructor.
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     */
    public function __construct(ContainerInterface $container, SettingsInterface $settings)
    {
        $this->container = $container;
        $this->settings = $settings;
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $symfonyRequest = $this->container->call([Request::class, 'createFromGlobals']);
        /** @var DiactorosFactory $psr7Factory */
        $psr7Factory = $this->container->make(DiactorosFactory::class);
        try {
            $psrRequest = $psr7Factory->createRequest($symfonyRequest);
        } catch (InvalidArgumentException $e) {
            // Defer creation of the CLI version as it requires SettingsInterface which is configured during the hook
            // phase.
            $psrRequest = function () use ($psr7Factory) {
                $symfonyRequest = $this->container->call(
                    [Request::class, 'create'],
                    [$this->settings['site']['default-hostname']]
                );
                $psrRequest = $psr7Factory->createRequest($symfonyRequest);

                $this->container['request'] = $psrRequest;
                $this->container->set(RequestInterface::class, $psrRequest);
                $this->container->set(ServerRequestInterface::class, $psrRequest);

                return $psrRequest;
            };
        }

        $this->container['request'] = $psrRequest;
        $this->container->set(RequestInterface::class, $psrRequest);
        $this->container->set(ServerRequestInterface::class, $psrRequest);
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        return [RequestInterface::class, ServerRequestInterface::class, 'request'];
    }
}
