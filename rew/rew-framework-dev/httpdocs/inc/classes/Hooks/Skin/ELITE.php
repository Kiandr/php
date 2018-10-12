<?php

use REW\Core\Interfaces\IDXInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\Hooks\SkinInterface as HooksSkinInterface;

/**
 * Elite skin hooks
 * @package Hooks_Skin
 */
class Hooks_Skin_ELITE implements HooksSkinInterface
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
     * @var LogInterface
     */
    private $log;

    /**
     * @var DBFactoryInterface
     */
    private $dbFactory;

    /**
     * List of locked idx panels
     * @var array
     */
    private $locked_panels = [
        'location',
        'price',
        'polygon',
        'radius',
        'bounds'
    ];

    /**
     * Hooks_Skin_ELITE constructor.
     * @param HooksInterface $hooks
     * @param SettingsInterface $settings
     * @param LogInterface $log
     * @param DBFactoryInterface $dbFactory
     */
    public function __construct(
        HooksInterface $hooks,
        SettingsInterface $settings,
        LogInterface $log,
        DBFactoryInterface $dbFactory
    ) {
    
        $this->hooks = $hooks;
        $this->settings = $settings;
        $this->log = $log;
        $this->dbFactory = $dbFactory;
    }

    /**
     * Setup skin specific hooks
     */
    public function initHooks()
    {
        // Attach Hook to manipulate REQUEST
        $this->hooks->on(HooksInterface::HOOK_IDX_SEARCH_REQUEST, array($this, 'idxSearchRequestHook'), 10);
        // Attach Hook to Load IDX Search Panel Override Settings
        $this->hooks->on(HooksInterface::HOOK_IDX_PANEL_SETTINGS, array($this, 'idxPanelSettingsHook'), 10);
        // Attach Hook to parse listing BEFORE the global parseListing is called
        $this->hooks->on(HooksInterface::HOOK_IDX_POST_PARSE_LISTING, array($this, 'parseListingHook'), 10);
        // Attach Hook to load full agent and office after agent is loaded
        $this->hooks->on(HooksInterface::HOOK_AGENT_INFO_LOADED, array($this, 'agentLoadedHook'), 10);
        // Attach Hook to control IDX panel settings on instantiation
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
     * This method is called whenever IDX panels are constructed.  This returns override values for this skin's search panels.
     * @param string $id
     * @return array or NULL
     */
    public function idxPanelSettingsHook($id)
    {
        // Don't do anything in the backend!
        if (Http_Uri::getBaseUri() == '/backend') {
            return null;
        }

        $shared = array(
            'setMarkupStyle' => 'uikit',
            'setHiddenClass' => 'uk-hidden',
            'setTitleClasses' => 'title uk-text-muted',
            'setTitleElement' => 'div',
            'setPanelClass' => 'fw-panel uk-width-1-1 filter-panel-box uk-width-medium-1-3',
            'setDetailsClass' => 'details uk-scrollable-box',
            'setContainerElement' => 'div',
            'setInputClass' => 'uk-width-1-1',
        );

        switch ($id) {
            case 'location':
            case 'mls':
            case 'type':
            case 'subtype':
            case 'address':
            case 'city':
            case 'area':
            case 'subdivision':
            case 'zip':
            case 'county':
            case 'school_elementary':
            case 'school_middle':
            case 'school_high':
            case 'school_district':
            case 'year':
            case 'dom':
            case 'dow':
            case 'age':
            case 'office':
            case 'office_id':
            case 'agent':
            case 'agent_id':
            case 'price':
            case 'reduced_price':
            case 'has_open_house':
                return array_merge($shared, array('setFormGroup' => Skin_ELITE::GROUP_PROPERTY_INFO));
            case 'bedrooms':
            case 'bathrooms':
            case 'rooms':
            case 'acres':
            case 'min_acres':
            case 'max_acres':
            case 'sqft':
            case 'min_sqft':
            case 'max_sqft':
            case 'stories':
            case 'garage_spaces':
                return array_merge($shared, array('setFormGroup' => Skin_ELITE::GROUP_PROPERTY_SIZE));
            case 'waterfront':
            case 'foreclosure':
            case 'shortsales':
            case 'bankowned':
            case 'features':
                return array_merge($shared, array('setFormGroup' => Skin_ELITE::GROUP_FEATURES));
            case 'polygon':
            case 'radius':
            case 'bounds':
                return $shared;
            case 'status':
                return array_merge($shared, array('setFormGroup' => Skin_ELITE::GROUP_STATUS));
            default:
                $this->log->halt('Unconfigured panel in use: ' . $id . '. Please configure in ' . __FILE__);
                die();
        }
    }

    /**
     * This method is called after the agent subdomain (or main site) is loaded.
     * @return array or NULL
     */
    public function agentLoadedHook()
    {
        $db = $this->dbFactory->get('cms');

        $query = "SELECT `o`.*, `s`.`country` FROM `" . $this->settings['TABLES']['LM_OFFICES'] . "` `o` JOIN `" . $this->settings['TABLES']['LM_AGENTS'] . "` `a` ON `a`.`office` = `o`.`id`"
            . " LEFT JOIN `" . $this->settings['TABLES']['LM_LOCATIONS'] . "` `s` ON `s`.`state` = `o`.`state`"
            . " WHERE `a`.`id` = " . ((int) $this->settings['SETTINGS']['agent']);
        $office = $db->fetch($query);

        $locations = Location::getLocations();
        if (!empty($office['state'])) {
            if (empty($locations[$office['country']]) || !($office['state_abbrev'] = array_search($office['state'], $locations[$office['country']]))) {
                $office['state_abbrev'] = $office['state'];
            }
        }

        $this->settings['SETTINGS']['cms']['office'] = $office;

        $query = "SELECT * FROM `" . $this->settings['TABLES']['LM_AGENTS'] . "`"
            . " WHERE `id` = " . ((int) $this->settings['SETTINGS']['agent']);
        $agent = $db->fetch($query);
        $this->settings['SETTINGS']['cms']['agent'] = $agent;
    }

    /**
     * This method is called at the end of Util_IDX::parseListing
     * @param array $listing
     * @param IDXInterface $idx
     * @return array or NULL
     */
    public function parseListingHook(array $listing, IDXInterface $idx)
    {

        global $_COMPLIANCE;

        // Days to consider a listing new
        $new_listing_days = $idx->getMaxAgeOfNewListingInDays();

        // A flag is already set
        if (!empty($listing['flag'])) {
            // Price Reduced
        } else if (!is_null($listing['ListingPriceOld']) && $listing['ListingPrice'] < $listing['ListingPriceOld']
                && (is_null($listing['ListingPriceChanged']) || strtotime($listing['ListingPriceChanged']) >= strtotime('-' . $new_listing_days . ' DAYS')) && $_COMPLIANCE['flags']['hide_price_reduction'] != true) {
            $listing['flag'] = 'Reduced';

            // New Listing
        } else if ((!is_null($listing['ListingDOM']) && $listing['ListingDOM'] <= $new_listing_days)
            || (!is_null($listing['ListingDOW']) && $listing['ListingDOW'] <= $new_listing_days)
            || (!is_null($listing['timestamp_created']) && strtotime($listing['timestamp_created']) >= strtotime('-' . $new_listing_days . ' DAYS'))
        ) {
            $listing['flag'] = 'New';
        }

        return $listing;
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
