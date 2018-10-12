<?php

namespace REW\Theme\Enterprise\Module\IdxPanelConstruct;

use REW\Core\Interfaces\InstallableInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\DBInterface;
use IDX_Panel;

/**
 * Idx Panel Locks and Blocks
 * @package REW\Theme\Enterprise\Module\IdxPanelController
 */
class ModuleController implements InstallableInterface
{

    /**
     * @var HooksInterface
     */
    protected $hooks;

    /**
     * List of locked idx panels
     * @var array
     */
    private $locked_panels = [
        'location',
        'price',
        'polygon',
        'radius',
        'bounds',
        'type',
        'rooms'
    ];

    /**
     * List of blocked idx panels
     * @var array
     */
    private $blocked_panels = [
        'bedrooms',
        'bathrooms'
    ];


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
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT, [$this, 'idxPanelConstructHook'], 10);
        $this->hooks->on(HooksInterface::HOOK_POST_CONTENT_INSTALL, [$this, 'postContentInstallHook'], 10);
    }

    /**
     * This method is called at the end of the IDX Panel constructor
     * @param IDX_Panel $idxPanel
     */
    public function idxPanelConstructHook(IDX_Panel $idxPanel)
    {


        if (in_array($idxPanel->getId(), $this->locked_panels)) {
            $idxPanel->setLocked(true);
        }

        if (in_array($idxPanel->getId(), $this->blocked_panels)) {
            $idxPanel->setBlocked(true);
        }
    }

    /**
     * Hook called after the site's DB content has been added during the DB installation process.
     * @param DBInterface $db
     */
    public function postContentInstallHook(DBInterface $db)
    {

        // Reset IDX Defaults
        $default = $db->fetch("SELECT `panels` FROM `rewidx_defaults` WHERE `idx` = '';");
        $panels = unserialize($default['panels']);

        // Defaults Panel Settings For Locked Panels
        $default_panel_settings = [
            "display", "1",
            "collapsed", "0",
            "hidden", "0"
        ];

        $panel_ids = array_keys($panels);

        // Add Locked Panels To The Defaults
        foreach ($this->locked_panels as $panel) {
            if (!in_array($panel, $panel_ids)) {
                $panels[$panel] = $default_panel_settings;
            }
        }

        // Remove Blocked Panels From The Defaults
        foreach ($panels as $panel) {
            if (in_array($panel, $this->blocked_panels)) {
                unset($panels[$panel]);
            }
        }

        $stmt = $db->prepare("UPDATE `rewidx_defaults` SET `panels` = :panels WHERE `idx` = '';");
        $stmt->execute(['panels' => serialize($panels)]);
    }
}
