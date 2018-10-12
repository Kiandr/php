<?php

use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

/**
 * LEC 2013 skin hooks
 * @package Hooks_Skin
 */
class Hooks_Skin_LEC2013 implements HooksSkinInterface
{

    /**
     * @var HooksInterface
     */
    private $hooks;

    /**
     * List of locked idx panels
     * @var array
     */
    private $locked_panels = [
        'location',
        'price',
    ];

    /**
     * Hooks_Skin_LEC2015 constructor.
     * @param HooksInterface $hooks
     */
    public function __construct(HooksInterface $hooks)
    {
        $this->hooks = $hooks;
    }

    /**
     * Setup skin specific hooks
     */
    public function initHooks()
    {
        // Attach Hook to control IDX panel settings on instantiation
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT, array($this, 'idxPanelConstructHook'), 10);
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

        $stmt = $db->prepare("UPDATE `rewidx_defaults` SET `panels` = :panels WHERE `idx` = '';");
        $stmt->execute(['panels' => serialize($panels)]);
    }
}
