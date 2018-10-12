<?php

/**
 * Hook_REW_BombBomb_Lead_SyncPartnersWhenAddingToGroup
 * Notifies BombBomb that a leads groups have changed
 *
 * @package Hooks
 */
class Hook_REW_BombBomb_Lead_SyncPartnersWhenAddingToGroup extends Hook_REW_BombBomb {

	/**
	 * Run the hook's code
	 * @param Backend_Lead $lead An instance of the backend lead
	 * @param Backend_Agent $agent An istance of a backend agent to update
	 * @param array $group
	 */
    protected function invoke (Backend_Lead $lead, Backend_Agent $agent, array $group) {

        // Require Adding To BombBomb
        if ($group['name'] !== Partner_BombBomb::GROUP_NAME || !empty($group['agent_id']) || $group['user'] !== 'false') return;

        // Require partner
        if (!($bombBomb = $this->getPartner($agent))) return;

        // Require list id
        if (!($bombBombId = $this->getListId())) return;

        // Add Contact
        $this->addContact($bombBomb, $bombBombId, $lead);

    }
}