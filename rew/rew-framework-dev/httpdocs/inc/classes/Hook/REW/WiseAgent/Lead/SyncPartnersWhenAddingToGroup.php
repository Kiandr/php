<?php

/**
 * Hook_REW_WiseAgent_Lead_SyncWithPartnersWhenAddingToGroup
 * Notifies WiseAgent that a leads groups have changed
 *
 * @package Hooks
 */
class Hook_REW_WiseAgent_Lead_SyncPartnersWhenAddingToGroup extends Hook_REW_WiseAgent {

    /**
     * Run this hook
     * @param Backend_Lead $lead
     * @param Backend_Agent $agent
     * @param array $groups
     */
    protected function invoke (Backend_Lead $lead, Backend_Agent $agent, array $group) {

        // Require Adding To WiseAgent
        if ($group['name'] !== Partner_WiseAgent::GROUP_NAME || !empty($group['agent_id']) || $group['user'] !== 'false') return;

        // Add Contact
        $this->addContact($agent, $lead);

    }
}