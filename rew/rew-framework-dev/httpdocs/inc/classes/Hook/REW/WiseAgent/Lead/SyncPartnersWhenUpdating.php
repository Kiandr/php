<?php

/**
 * Hook_REW_WiseAgent_Lead_SyncWithPartnersWhenUpdating
 * Notifies WiseAgent that a leads groups have changed
 *
 * @package Hooks
 */
class Hook_REW_WiseAgent_Lead_SyncPartnersWhenUpdating extends Hook_REW_WiseAgent {

	/**
	 * Run the hook's code
	 * @param Backend_Lead $lead An instance of the backend lead
	 * @param Backend_Agent $agent
	 * @param array $groups
	 */
    protected function invoke (Backend_Lead $lead, Backend_Agent $agent, array $groups) {

        // Check if in Group
        $inGroup = false;
        foreach ($groups AS $group) {
            if ($group['name'] === Partner_WiseAgent::GROUP_NAME && empty($group['agent_id']) && $group['user'] === 'false') {
                $inGroup = true;
            }
        }
        if (!$inGroup) return;

        // Add Contact
        $this->addContact($agent, $lead);

    }
}