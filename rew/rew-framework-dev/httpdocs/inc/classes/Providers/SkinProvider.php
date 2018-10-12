<?php

namespace REW\Providers;

use Skin_Backend;
use REW\Provider;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\SkinProviderInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

class SkinProvider extends Provider implements SkinProviderInterface
{

    /**
     * @const string[]
     */
    const SKIN_CLASS_PATTERNS = [
        'REW\\Theme\\%s\\Theme',
        'Skin_%s'
    ];

    /**
     * @const string[]
     */
    const HOOK_CLASS_PATTERNS = [
        'Hooks_Skin_%s'
    ];

    /**
     * @var LogInterface
     */
    private $log;

    /**
     * SkinProvider constructor.
     * @param ContainerInterface $container
     * @param LogInterface $log
     */
    public function __construct(ContainerInterface $container, LogInterface $log)
    {
        parent::__construct($container);
        $this->log = $log;
    }

    /**
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();
        $skinName = $container['settings']['SKIN'];
        $schemeName = $container['settings']['SKIN_SCHEME'];

        $container['skin'] = $skin = $this->buildSkinInstance($skinName, $schemeName);
        $container->set(SkinInterface::class, $skin);

        $skinHook = $this->buildSkinHookInstance($skinName);
        if ($skinHook) {
            $container['skin-hook'] = $skinHook;
            $container->set(HooksSkinInterface::class, $skinHook);
        } else {
            $this->log->debug('We could not find a class for hooks belonging to the current skin.');
        }

        $container['skin-backend'] = $backendSkin = $container->get(Skin_Backend::class);
        $container['skin-provider'] = $this;
        $container->set(SkinProviderInterface::class, $this);
    }

    /**
     * Builds and returns an instance of the correct skin for $skin and $scheme
     * @param string $skin
     * @param string $scheme
     * @return mixed
     */
    public function buildSkinInstance($skin, $scheme)
    {
        $container = $this->getContainer();
        $className = class_exists($skin) ? $skin : $this->getClass($skin, self::SKIN_CLASS_PATTERNS);
        return $container->make($className, ['scheme' => $scheme]);
    }

    /**
     * Builds and returns an instance of the correct hooks class for $skin
     * @param string $skin
     * @return mixed
     */
    public function buildSkinHookInstance($skin)
    {
        $container = $this->getContainer();
        if (class_exists($skin)) {
            return null;
        }
        $className = $this->getClass($skin, self::HOOK_CLASS_PATTERNS);
        if ($className && $container->has($className)) {
            return $container->get($className);
        }
        return null;
    }

    /**
     * Find the appropriate class name for the given skin
     * @param string $skinName
     * @param array $patterns
     * @return string|null
     */
    public function getClass($skinName, $patterns)
    {
        $skinName = str_replace('-', '', $skinName);
        $tryClasses = [];
        foreach ($patterns as $pattern) {
            $tryClasses[] = sprintf($pattern, ucwords($skinName));
            $tryClasses[] = sprintf($pattern, strtoupper($skinName));
        }
        $container = $this->getContainer();
        foreach ($tryClasses as $tryClass) {
            if ($container->has($tryClass)) {
                return $tryClass;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['skin', 'skin-backend', 'skin-hook', 'skin-provider', SkinInterface::class, HooksSkinInterface::class,
            SkinProviderInterface::class];
    }
}
