<?php

/**
 * Hook_REW_HappyGrasshopper_Lead_SyncPartnersWhenAddingToGroup
 * Notifies HappyGrasshopper that a leads groups have changed
 *
 * @package Hooks
 */
class Hook_REW_HappyGrasshopper_Lead_SyncPartnersWhenAddingToGroup extends Hook_REW_HappyGrasshopper {

    /**
     * Run the hook's code
     * @param Backend_Lead $lead
     * @param Backend_Agent $agent
     * @param array $group
     * @param array $groups
     */
    protected function invoke (Backend_Lead $lead, Backend_Agent $agent, array $group, array $groups = []) {

        if (!($happyGrasshopper = $this->getPartner($agent))) return;

        // Added to Happy Grasshopper Sync
        if ($group['name'] === Partner_HappyGrasshopper::GROUP_NAME && empty($group['agent_id']) && $group['user'] === 'false') {

            // Get non-system groups
            $groupNames = $this->getNonSystemGroupNames($groups);

            // Update Existing Partner Contact
            $dataId = $this->getDataId($lead);
            if (!empty($dataId) && $dataId != 0) {
                $happyGrasshopper->updateContact(
                    $dataId,
                    $lead->info('first_name'),
                    $lead->info('last_name'),
                    $lead->info('email'),
                    $groupNames
                );
                return;
            }

            // Create New Partner Contact
            $this->addContact($happyGrasshopper, $lead, $groupNames);

        // New Happy Grasshopper Tag
        } else {

            if (in_array($group['name'], $this->getSystemGroupNames())) return;

            // Check if in Group
            $inGroup = false;
            foreach ($groups AS $group) {
                if ($group['name'] === Partner_HappyGrasshopper::GROUP_NAME && empty($group['agent_id']) && $group['user'] === 'false') {
                    $inGroup = true;
                }
            }
            if (!$inGroup) return;

            $dataId = $this->getDataId($lead);
            if (!empty($dataId) && $dataId != 0) {
                $happyGrasshopper->addTag($dataId, $group['name']);
                return;
            }

            // Create New Partner Contact
            $groupNames = $this->getNonSystemGroupNames($groups);
            $this->addContact($happyGrasshopper, $lead, $groupNames);

        }

    }
}