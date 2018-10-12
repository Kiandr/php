<?php

use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

/**
 * BCSE skin hooks
 * @package Hooks_Skin
 */
class Hooks_Skin_BCSE implements HooksSkinInterface
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
     * Hooks_Skin_BCSE constructor.
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
        $this->hooks->on(HooksInterface::HOOK_IDX_SEARCH_REQUEST, array($this, 'idxSearchRequestHook'), 10);
        // Attach Hook to Load IDX Search Panel Override Settings
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_SETTINGS, array($this, 'idxPanelSettingsHook'), 10);
        // Attach hook to control IDX panel settings on instantiation
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT, array($this, 'idxPanelConstructHook'), 10);
        // Attach hook to run after installation of DB content
        $this->hooks->on(HooksInterface::HOOK_POST_CONTENT_INSTALL, array($this, 'postContentInstallHook'), 10);
    }

    /**
     * This method is called on the search results to modify the global $_REQUEST data
     * @param array $request
     * @return array
     */
    public function idxSearchRequestHook($request)
    {

        // Property features
        $features = $request['search_features'];
        if (!empty($features) && is_array($features)) {
            foreach ($features as $feature) {
                if (in_array($feature, array(
                    'search_pool',
                    'search_waterfront',
                    'search_fireplace'
                ))) {
                    $request[$feature] = 'Y';
                }
            }
        }

        // Return $_REQUEST
        return $request;
    }

    /**
     * This method is called whenever IDX panels are constructed.  This returns overide values for this skin's search panels.
     * @param string $id
     * @return array or NULL
     */
    public function idxPanelSettingsHook($id)
    {
        switch ($id) {
            case 'status':
                return array ('setFieldType' => 'Select');
                break;
            case 'dom':
                return array ('setFieldType' => 'Select',
                              'setFieldOptions' => array('placeholder' => false));
                break;
            default:
                return null;
        }
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
