<?php

use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

/**
 * LEC-2015 skin hooks
 * @package Hooks_Skin
 */
class Hooks_Skin_LEC2015 implements HooksSkinInterface
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
        // Attach hook to handle modifying of search $_REQUEST criteria
        $this->hooks->on(HooksInterface::HOOK_IDX_SEARCH_REQUEST, array($this, 'idxSearchRequestHook'), 10);
        // Attach Hook to Load IDX Search Panel Override Settings
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_SETTINGS, array($this, 'idxPanelSettingsHook'), 10);

        // Attach hooks to handle CMS snippet validation & preprocessing
        $this->hooks->on(HooksInterface::HOOK_CMS_SNIPPET_SAVED, array($this, 'cmsSnippetSaveHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_CMS_SNIPPET_VALIDATE, array($this, 'cmsSnippetValidateHook'), 10);

        // Attach hooks to handle changes to agent accounts
        $this->hooks->on(HooksInterface::HOOK_AGENT_CREATE, array($this, 'agentCreateHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_AGENT_DELETE, array($this, 'agentDeleteHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_AGENT_UPDATE, array($this, 'agentUpdateHook'), 10);

        // Attach hooks to handle changes to featured listings
        $this->hooks->on(HooksInterface::HOOK_FEATURED_LISTING_CREATE, array($this, 'featuredListingCreateHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_FEATURED_LISTING_DELETE, array($this, 'featuredListingDeleteHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_FEATURED_LISTING_UPDATE, array($this, 'featuredListingUpdateHook'), 10);

        // Attach hooks to handle changes to featured communities
        $this->hooks->on(HooksInterface::HOOK_FEATURED_COMMUNITY_CREATE, array($this, 'featuredCommunityCreateHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_FEATURED_COMMUNITY_DELETE, array($this, 'featuredCommunityDeleteHook'), 10);
        $this->hooks->on(HooksInterface::HOOK_FEATURED_COMMUNITY_UPDATE, array($this, 'featuredCommunityUpdateHook'), 10);

        // Attach hooks to control IDX panel settings on instantiation
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
     * Trigger this hook to validate a snippet before saving
     * @param array $snippet Snippet to be saved
     * @param array|NULL $original Original snippet
     * @throws InvalidArgumentException
     */
    public function cmsSnippetValidateHook(array $snippet, array $original = null)
    {

        // Cannot rename #navigation# snippet
        $cannot_rename = array('navigation' ,'phone-number', 'footer-contact', 'footer-links', 'social-media');
        if (!empty($original) && in_array($original['name'], $cannot_rename)) {
            if ($original['name'] !== $snippet['name']) {
                throw new InvalidArgumentException('The #' . $original['name'] . '# snippet cannot be renamed');
            }
        }

        // Validate #navigation# snippet
        if ($snippet['name'] === 'navigation') {
            // Ensure valid HTML code
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $load = $dom->loadHTML($snippet['code']);

            // Check for parser errors
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                $errors = array_filter((array) $errors, function ($error) {
                    return strpos($error->message, 'htmlParseEntityRef') !== 0;
                });
                if (!empty($errors)) {
                    throw new InvalidArgumentException('This snippet must contain valid HTML code');
                }
            }
        }
    }

    /**
     * Trigger this hook after a snippet is saved
     * @param array $snippet Snippet that was saved
     * @param array $original|NULL Original snippet record
     */
    public function cmsSnippetSaveHook(array $snippet, array $original = null)
    {

        // CMS snippet saved: #navigation#
        if ($snippet['name'] === 'navigation') {
            $this->_flushNavigationCache();
        }
    }

    /**
     * Flush cache when agent is created
     * @param array $agent
     */
    public function agentCreateHook($agent)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when agent is updated
     * @param array $agent
     */
    public function agentUpdateHook($agent)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when agent is deleted
     * @param array $agent
     */
    public function agentDeleteHook($agent)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when featured community is created
     * @param array $community
     */
    public function featuredCommunityCreateHook($community)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when featured community is updated
     * @param array $community
     */
    public function featuredCommunityUpdateHook($community)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when featured community is deleted
     * @param array $community
     */
    public function featuredCommunityDeleteHook($community)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when featured listing is created
     * @param string $idx_feed
     * @param string $mls_number
     */
    public function featuredListingCreateHook($idx_feed, $mls_number)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when featured listing is updated
     * @param string $idx_feed
     * @param string $mls_number
     */
    public function featuredListingUpdateHook($idx_feed, $mls_number)
    {
        $this->_flushNavigationCache();
    }

    /**
     * Flush cache when featured listing is deleted
     * @param string $idx_feed
     * @param string $mls_number
     */
    public function featuredListingDeleteHook($idx_feed, $mls_number)
    {
        $this->_flushNavigationCache();
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

    /**
     * Flush cached output for LEC navigation
     */
    protected function _flushNavigationCache()
    {
        Cache::setCache(Skin_LEC2015::NAVIGATION_CACHE_INDEX, null);
    }
}
