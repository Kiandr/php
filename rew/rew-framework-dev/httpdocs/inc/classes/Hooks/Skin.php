<?php

use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

/**
 * Abstract interface used to load skin-specific hooks
 * @package Hooks
 */
class Hooks_Skin implements HooksSkinInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * Skin instance
     * @var Skin
     */
    protected $skin;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Initialize
     * @param SkinInterface $skin
     * @param ContainerInterface $container
     */
    public function __construct(SkinInterface $skin, ContainerInterface $container)
    {
        $this->skin = $skin;
        $this->container = $container;
    }

    /**
     * Initialize skin hooks
     * @return HooksSkinInterface|null
     */
    public function initHooks()
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HooksSkinInterface::class, __FUNCTION__, func_get_args());
        }

        $hooksInstance = $this->container->has('skin-hook') ? $this->container->get('skin-hook') : null;
        if ($hooksInstance && is_subclass_of($hooksInstance, HooksSkinInterface::class)) {
            $hooksInstance->initHooks();
        }
    }
}
