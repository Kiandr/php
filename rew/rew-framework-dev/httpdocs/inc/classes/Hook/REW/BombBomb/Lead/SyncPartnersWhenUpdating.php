<?php

/**
 * Hook_REW_BombBomb_Lead_SyncPartnersWhenUpdating
 * Notifies BombBomb that a leads groups have changed
 *
 * @package Hooks
 */
class Hook_REW_BombBomb_Lead_SyncPartnersWhenUpdating extends Hook_REW_BombBomb {

    /**
     * Run the hook's code
     * @param Backend_Lead $lead
     * @param Backend_Agent $agent
     * @param array $groups
     */
    protected function invoke (Backend_Lead $lead, Backend_Agent $agent, array $groups) {

        // Check if in Group
        $inGroup = false;
        foreach ($groups AS $group) {
            if ($group['name'] === Partner_BombBomb::GROUP_NAME && empty($group['agent_id']) && $group['user'] === 'false') {
                $inGroup = true;
            }
        }
        if (!$inGroup) return;

        // Require partner
        if (!($bombBomb = $this->getPartner($agent))) return;

        // Require list id
        if (!($bombBombId = $this->getListId())) return;

        // Add Contact
        $this->addContact($bombBomb, $bombBombId, $lead);

    }
}