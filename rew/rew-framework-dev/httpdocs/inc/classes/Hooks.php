<?php

use REW\Core\Interfaces\HookInterface;
use REW\Core\Interfaces\HooksInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\Hooks\SkinInterface;
use REW\Core\Interfaces\Hook\CollectionInterface;

/**
 * Hooks
 * @package Hooks
 */
class Hooks implements HooksInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * Whether the hooks have been initialized
     * @var boolean
     */
    protected $initialized = false;

    /**
     * Registered hooks
     * @var array
     */
    protected $hooks = array();

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SkinInterface
     */
    protected $skinHooks;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Hooks constructor.
     * @param ContainerInterface $container
     * @param SkinInterface $skinHooks
     * @param SettingsInterface $settings
     */
    public function __construct(ContainerInterface $container, SkinInterface $skinHooks, SettingsInterface $settings)
    {
        $this->container = $container;
        $this->skinHooks = $skinHooks;
        $this->settings = $settings;
    }

    /**
     * Define all hook handlers
     */
    public function initHooks()
    {
        if (!$this instanceof self) {
            self::callInstanceMethod(HooksInterface::class, __FUNCTION__, func_get_args());
            return;
        }

        // Don't even run this twice
        if ($this->initialized === true) {
            return;
        }

        // Core Hooks
        $this->on(static::HOOK_LEAD_FORM_SUBMISSION, 'REW_Core_Lead_FormSubmission', 10);
        $this->on(static::HOOK_LEAD_TEXT_INCOMING, 'REW_Core_Lead_IncomingText', 10);

        // Shark Tank
        if (!empty($this->settings['MODULES']['REW_SHARK_TANK'])) {
            $handler_class = 'REW_SharkTank_';
            $this->on(static::HOOK_LEAD_CREATED, $handler_class . 'Lead_Created', 10);
        }

        // Follow Up Boss
        if (!empty($this->settings['MODULES']['REW_PARTNERS_FOLLOWUPBOSS'])) {
            $handler_class = 'REW_FollowUpBoss_';
            $this->on(static::HOOK_LEAD_VISIT, $handler_class . 'Lead_Visit', 10);
            $this->on(static::HOOK_LEAD_FORM_SUBMISSION, $handler_class . 'Lead_FormSubmission', 10);
            $this->on(static::HOOK_LEAD_SEARCH_SAVED, $handler_class . 'Lead_SearchSaved', 10);
            $this->on(static::HOOK_LEAD_SEARCH_PERFORMED, $handler_class . 'Lead_SearchPerformed', 10);
            $this->on(static::HOOK_LEAD_LISTING_SAVED, $handler_class . 'Lead_ListingSaved', 10);
            $this->on(static::HOOK_LEAD_LISTING_VIEWED, $handler_class . 'Lead_ListingViewed', 10);
            $this->on(static::HOOK_AGENT_CALL_OUTGOING, $handler_class . 'Agent_OutgoingCall', 10);
        }

        // Zillow
        if (!empty($this->settings['MODULES']['REW_PARTNERS_ZILLOW'])) {
            $handler_class = 'REW_Zillow_';
            $this->on(static::HOOK_AGENT_LEAD_ACCEPT, $handler_class . 'Lead_Accepted', 10);
            $this->on(static::HOOK_AGENT_LEAD_REASSIGN, $handler_class . 'Lead_Reassigned', 10);
            $this->on(static::HOOK_AGENT_LEAD_REJECT, $handler_class . 'Lead_Rejected', 10);
            $this->on(static::HOOK_AGENT_LEAD_CAMPAIGN_START, $handler_class . 'Lead_CampaignStarted', 10);
            $this->on(static::HOOK_AGENT_LEAD_CAMPAIGN_END, $handler_class . 'Lead_CampaignEnded', 10);
        }

        // First Call Agent
        if (!empty($this->settings['MODULES']['REW_PARTNERS_FIRSTCALLAGENT'])) {
            $handler_class = 'REW_FirstCallAgent_';
            $this->on(static::HOOK_LEAD_CREATED, $handler_class . 'Lead_Created', 10);
            $this->on(static::HOOK_AGENT_LEAD_REASSIGN, $handler_class . 'Agent_Lead_Reassign', 10);
        }

        // Moxi Works CRM
        if (!empty($this->settings['MODULES']['REW_PARTNERS_MOXI_CRM'])) {
            $handler_class = 'REW_Moxiworks_';
            $this->on(static::HOOK_LEAD_CREATED, $handler_class . 'Lead_Created', 10);
        }

        // Outgoing API
        if (!empty($this->settings['MODULES']['REW_CRM_API_OUTGOING'])) {
            $handler_class = 'REW_OutgoingAPI_';
            $this->on(static::HOOK_LEAD_VISIT, $handler_class . 'Lead_Visit', 10);
            $this->on(static::HOOK_LEAD_FORM_SUBMISSION, $handler_class . 'Lead_FormSubmission', 10);

            $this->on(static::HOOK_LEAD_SEARCH_SAVED, $handler_class . 'Lead_SearchSaved', 10);
            $this->on(static::HOOK_LEAD_SEARCH_REMOVED, $handler_class . 'Lead_SearchRemoved', 10);
            $this->on(static::HOOK_LEAD_SEARCH_PERFORMED, $handler_class . 'Lead_SearchPerformed', 10);
            $this->on(static::HOOK_LEAD_LISTING_SAVED, $handler_class . 'Lead_ListingSaved', 10);
            $this->on(static::HOOK_LEAD_LISTING_REMOVED, $handler_class . 'Lead_ListingRemoved', 10);
            $this->on(static::HOOK_LEAD_LISTING_VIEWED, $handler_class . 'Lead_ListingViewed', 10);
        }

        // BombBomb
        if (!empty($this->settings['MODULES']['REW_PARTNERS_BOMBBOMB'])) {
            $handler_class = 'REW_BombBomb_';
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_UPDATING, $handler_class . 'Lead_SyncPartnersWhenUpdating', 10);
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_ADDING_GROUP, $handler_class . 'Lead_SyncPartnersWhenAddingToGroup', 10);
        }

        // Happy Grasshopper
        if (!empty($this->settings['MODULES']['REW_PARTNERS_GRASSHOPPER'])) {
            $handler_class = 'REW_HappyGrasshopper_';
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_UPDATING, $handler_class . 'Lead_SyncPartnersWhenUpdating', 10);
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_ADDING_GROUP, $handler_class . 'Lead_SyncPartnersWhenAddingToGroup', 10);
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_REMOVING_GROUP, $handler_class . 'Lead_SyncPartnersWhenRemovingGroup', 10);
        }

        // WiseAgent
        if (!empty($this->settings['MODULES']['REW_PARTNERS_WISEAGENT'])) {
            $handler_class = 'REW_WiseAgent_';
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_UPDATING, $handler_class . 'Lead_SyncPartnersWhenUpdating', 10);
            $this->on(static::HOOK_LEAD_SYNC_PARTNER_ADDING_GROUP, $handler_class . 'Lead_SyncPartnersWhenAddingToGroup', 10);
        }

        // Load skin-specific hooks
        $this->skinHooks->initHooks();

        // Please don't run this again
        $this->initialized = true;

        // Run the bind hook now that everything is done.
        $this->hook(static::HOOK_DEPENDENCY_BINDING)->run($this->container);
    }

    /**
     * Define an implementation to run for a given hook
     * @param string $name
     * @param callable $callable
     * @param integer $priority
     * @throws Exception
     * @return Hook
     */
    public function on($name, $callable, $priority)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HooksInterface::class, __FUNCTION__, func_get_args());
        }

        // Hook instance
        $priority = intval($priority);
        $hook = null;

        // Object callable
        if (!is_callable($callable)) {
            $parts = explode('.', $callable);

            // Hook object class
            $class_name = 'Hook_' . implode('_', $parts);
            if (!$this->container->has($class_name)) {
                throw new Exception(__METHOD__ . ' could not resolve hook implementation object \'' . $class_name . '\' for name \'' . $name . '\'');
            }

            // Create instance
            $hook = $this->container->make($class_name, ['name' => $name, 'priority' => $priority]);
        } else {
            // Require supported callable
            if (!is_callable($callable)) {
                throw new Exception(__METHOD__ . ' received invalid callable implementation for hook \'' . $name . '\'');
            }

            // Basic hook instance
            $hook = $this->container->make(
                HookInterface::class,
                ['name' => $name, 'priority' => $priority, 'callable' => $callable]
            );
        }

        // Add to collection
        $this->hooks[$name][$priority][] = $hook;

        return $hook;
    }

    /**
     * Get a collection of implementations for a given hook
     * @param string $name The hook name
     * @throws Exception
     * @return CollectionInterface
     */
    public function hook($name)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(HooksInterface::class, __FUNCTION__, func_get_args());
        }

        // Require defined hook
        if (!isset($this->hooks[$name])) {
            return $this->container->make(CollectionInterface::class, ['hooks' => []]);
        }

        // Sort by priority
        ksort($this->hooks[$name]);

        // Collect hooks
        $hooks = array();
        foreach ($this->hooks[$name] as $priority) {
            if (!empty($priority)) {
                foreach ($priority as $hook) {
                    $hooks[] = $hook;
                }
            }
        }

        // Create hook collection
        $collection = $this->container->make(CollectionInterface::class, ['hooks' => $hooks]);
        return $collection;
    }
}
