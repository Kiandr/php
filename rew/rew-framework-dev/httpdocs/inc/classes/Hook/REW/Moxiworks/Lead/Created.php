<?php

/**
 * Hook_REW_Moxiworks_Lead_Created
 * This hook is invoked on lead register/connect for new leads only
 * @package Hooks
 */
class Hook_REW_Moxiworks_Lead_Created extends Hook_REW_Moxiworks
{
    /**
     * Invoke this hook
     *
     * @param Backend_Lead $lead
     * @param bool $manually_created
     * @return void
     */
    protected function invoke($lead, $manually_created = false)
    {
        $moxiworks = $this->getPartner();
        try {
            $check_duplicate = $moxiworks->getContactByEmail($lead->info('email'));
            if (!isset($check_duplicate['contacts']) || empty($check_duplicate['contacts'])) {
                $moxiworks->pushContact($lead);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
