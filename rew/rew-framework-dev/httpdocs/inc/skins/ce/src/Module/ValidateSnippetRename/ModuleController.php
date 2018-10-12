<?php

namespace REW\Theme\Enterprise\Module\ValidateSnippetRename;

use REW\Core\Interfaces\InstallableInterface;
use REW\Core\Interfaces\InstallerInterface;
use REW\Core\Interfaces\HooksInterface;
use \InvalidArgumentException;

/**
 * Validate content snippets to prevent renaming of required snippets
 * @package REW\Theme\Enterprise\Module\ValidateSnippetRename
 */
class ModuleController implements InstallableInterface
{

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * @var InstallerInterface
     */
    protected $installer;

    /**
     * @param HooksInterface $hooks
     */
    public function __construct(HooksInterface $hooks, InstallerInterface $installer)
    {
        $this->hooks = $hooks;
        $this->installer = $installer;
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {
        $this->hooks->on(HooksInterface::HOOK_CMS_SNIPPET_VALIDATE, [$this, 'cmsSnippetValidateHook'], 10);
    }

    /**
     * @param array $snippet Snippet to be saved
     * @param array|NULL $original Original snippet
     * @throws InvalidArgumentException
     */
    public function cmsSnippetValidateHook(array $snippet, array $original = null)
    {
        $snippets = $this->installer->getSnippets();
        if (!empty($original) && isset($snippets[$original['name']])) {
            if ($original['name'] !== $snippet['name']) {
                throw new InvalidArgumentException(sprintf(
                    'The #%s# snippet cannot be renamed.',
                    $original['name']
                ));
            }
        }
    }
}
