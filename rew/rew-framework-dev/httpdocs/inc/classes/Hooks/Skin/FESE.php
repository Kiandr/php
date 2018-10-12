<?php

use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

/**
 * FESE skin hooks
 * @package Hooks_Skin
 */
class Hooks_Skin_FESE implements HooksSkinInterface
{
    /**
     * @var HooksInterface
     */
    private $hooks;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var SkinInterface
     */
    private $skin;

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
     * Hooks_Skin_FESE constructor.
     * @param HooksInterface $hooks
     * @param SettingsInterface $settings
     * @param SkinInterface $skin
     */
    public function __construct(HooksInterface $hooks, SettingsInterface $settings, SkinInterface $skin)
    {
        $this->hooks = $hooks;
        $this->settings = $settings;
        $this->skin = $skin;
    }

    /**
     * Setup skin specific hooks
     */
    public function initHooks()
    {
        $this->hooks->on(HooksInterface::HOOK_SITE_PAGE_LOAD, [$this, 'sitePageLoadHook'], 10);
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT, [$this, 'idxPanelConstructHook'], 10);
        $this->hooks->on(HooksInterface::HOOK_CMS_SNIPPET_VALIDATE, [$this, 'cmsSnippetValidateHook'], 10);
        $this->hooks->on(HooksInterface::HOOK_IDX_POST_PARSE_LISTING, [$this, 'idxPostParseListingHook'], 10);
        // Attach Hook to control IDX panel settings on instantiation
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_CONSTRUCT, array($this, 'idxPanelConstructHook'), 10);
        // Attach hook to run after installation of DB content
        $this->hooks->on(HooksInterface::HOOK_POST_CONTENT_INSTALL, array($this, 'postContentInstallHook'), 10);
        // Disable slideshow manager (this skin does not use it)
        $this->settings['MODULES']['REW_SLIDESHOW_MANAGER'] = false;
        // Disable featured listings over-ride (has no effect on this skin)
        $this->settings['MODULES']['REW_FEATURED_LISTINGS_OVERRIDE'] = false;
    }

    /**
     * This method is called at the end of the IDX Panel constructor
     * @param IDX_Panel $panel
     */
    public function idxPanelConstructHook(IDX_Panel $panel)
    {
        // Change price reduce panel to use <select> field
        if ($panel instanceof IDX_Panel_ReducedPrice) {
            $panel->setFieldType('Select');
        }

        if (in_array($panel->getId(), $this->locked_panels)) {
            $panel->setLocked(true);
        }

        if (in_array($panel->getId(), $this->blocked_panels)) {
            $panel->setBlocked(true);
        }
    }

    /**
     * Disable the ability to rename core skin snippets
     * @param array $snippet Snippet to be saved
     * @param array|NULL $original Original snippet
     * @throws InvalidArgumentException
     */
    public function cmsSnippetValidateHook(array $snippet, array $original = null)
    {
        $snippets = Installer::getSnippets();
        if (!empty($original) && isset($snippets[$original['name']])) {
            if ($original['name'] !== $snippet['name']) {
                throw new InvalidArgumentException(sprintf(
                    'The #%s# snippet cannot be renamed.',
                    $original['name']
                ));
            }
        }
    }

    /**
     * Adjust listing placeholder image
     * @param array $listing
     * @param IDXInterface $idx
     * @return array
     */
    public function idxPostParseListingHook(array $listing, IDXInterface $idx)
    {
        if (preg_match('/no\-image\.jpg$/', $listing['ListingImage'])) {
            $listing['ListingImage'] = sprintf(
                '%s%s/img/no-image.jpg',
                Http_Host::getDomainUrl(),
                ltrim($this->skin->getUrl(), '/')
            );
        }
        return $listing;
    }

    /**
     * New development page URLs
     * @param array $row
     * @param DBInterface $db
     * @param PageInterface $page
     * @param $id
     * @return array
     */
    public function sitePageLoadHook($row = [], DBInterface $db, PageInterface $page, $id)
    {
        $url_pattern = '#/development/([a-zA-Z0-9\-]*)/$#';
        if (preg_match($url_pattern, Http_Uri::getUri(), $matches)) {
            $link = $matches[1];
            $querySelect = "`id`, `title`, `description`, `page_title`, `meta_description`";
            $queryString = sprintf("SELECT %s FROM `developments` WHERE `link` = ? LIMIT 1;", $querySelect);
            $query = $db->prepare($queryString);
            $query->execute([$link]);
            if ($development = $query->fetch()) {
                return [
                    'file_name' => 'development',
                    'page_title' => $development['page_title'] ?: $development['title'],
                    'meta_tag_desc' => $development['meta_description'] ?: $development['description'],
                    'category_html' => $page->container('snippet')->module('developments', [
                        'details'    => $development['id'],
                        'thumbnails' => false,
                        'listings'   => 6
                    ])->display(false)
                ];
            }
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
