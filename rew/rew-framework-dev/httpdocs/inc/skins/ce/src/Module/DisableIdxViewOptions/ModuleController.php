<?php

namespace REW\Theme\Enterprise\Module\DisableIdxViewOptions;

use REW\Core\Interfaces\InstallableInterface;
use REW\Core\Interfaces\HooksInterface;

/**
 * Disable view options for IDX builder pages
 * @package REW\Theme\Enterprise\Module\DisableIdxViewOptions
 */
class ModuleController implements InstallableInterface
{

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @param HooksInterface $hooks
     */
    public function __construct(HooksInterface $hooks)
    {
        $this->hooks = $hooks;
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {
        $this->hooks->on(HooksInterface::HOOK_IDX_BUILDER_VIEW_OPTIONS, [$this, 'idxBuilderViewOptionsHook'], 10);
    }

    /**
     * @param array $viewOptions
     * @return array
     */
    public function idxBuilderViewOptionsHook(array $viewOptions)
    {
        return [];
    }
}
