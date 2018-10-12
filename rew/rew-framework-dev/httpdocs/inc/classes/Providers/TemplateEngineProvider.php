<?php

namespace REW\Providers;

use REW\Core\Interfaces\Http\HostInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ProviderInterface;
use REW\View\Interfaces\FactoryInterface;
use REW\View\Engine\TwigEngine;

/**
 * Template Engine Provider
 * @package REW\Providers
 */
class TemplateEngineProvider implements ProviderInterface {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var HostInterface
     */
    protected $httpHost;

    /**
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     * @param HostInterface $httpHost
     */
    public function __construct(ContainerInterface $container, SettingsInterface $settings, HostInterface $httpHost) {
        $this->container = $container;
        $this->settings = $settings;
        $this->httpHost = $httpHost;
    }

    /**
     * {@inheritDoc}
     */
    public function register() {
        $factory = $this->container->get(FactoryInterface::class);
        $this->registerTwigEngine($this->container, $this->settings, $factory);
    }

    /**
     * {@inheritDoc}
     */
    public function boot() {
    }

    /**
     * {@inheritDoc}
     */
    public function provides() {
        return [];
    }

    /**
     * Register twig template engine
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     * @param FactoryInterface $factory
     */
    protected function registerTwigEngine (ContainerInterface $container, SettingsInterface $settings, FactoryInterface $factory) {
        $twigOptions = $this->getTwigOptions($settings);
        $factory->registerEngine('twig', function () use ($container, $factory, $twigOptions) {
            return $container->make(TwigEngine::class, [
                'loader' => $factory->getLoader(),
                'envOpts' => array_merge($twigOptions, [
                    //'debug' => $this->httpHost->isDev()
                ])
            ]);
        });
    }

    /**
     * @param SettingsInterface $settings
     * @return array
     */
    protected function getTwigOptions (SettingsInterface $settings) {
        return array_filter($settings['view']['twig'], function ($option) {
            if ($option === 'cache' && $this->httpHost->isDev()) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);
    }

}