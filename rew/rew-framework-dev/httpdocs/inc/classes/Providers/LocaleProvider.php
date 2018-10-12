<?php

namespace REW\Providers;

use REW\Provider;
use Gettext\GettextTranslator;
use Gettext\TranslatorInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SettingsInterface;

class LocaleProvider extends Provider
{

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     * @param SettingsInterface $settings
     */
    public function __construct(
        ContainerInterface $container,
        SettingsInterface $settings
    ) {
        parent::__construct($container);
        $this->settings = $settings;
    }

    /**
     * @return void
     */
    public function register()
    {

        $container = $this->getContainer();
        $translator = $container->get(GettextTranslator::class);

        // Setup translation support
        $localeSettings = $this->settings['localization'];
        $localePath = __DIR__ . '/../../../../' . $localeSettings['path'];
        if (is_dir($localePath)) {
            $translator->setLanguage($localeSettings['default_locale']);
            $translator->loadDomain($localeSettings['domain'], $localePath);
        }

        // Register global functions
        $translator->register();

        // Bind to DIC
        $container->set(TranslatorInterface::class, $translator);

    }

    /**
     * @return array
     */
    public function provides()
    {
        return [TranslatorInterface::class];
    }

}
