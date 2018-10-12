<?php

/**
 * Hook_REW_WiseAgent
 * Base class for WiseAgent hooks
 *
 * @package Hooks
 */
class Hook_REW_WiseAgent extends Hook {

    /**
     * Update Contact
     * @param Backend_Agent $agent
     * @param Backend_Lead $lead
     * @param array $group
     */
    protected function addContact (Backend_Agent $agent, Backend_Lead $lead) {

        $integrationSettings = Backend_Agent::load(1);

        // Require Partners
        $partners = json_decode($integrationSettings->info('partners'), true);
        if (empty($partners)) return;
        // Require WiseAgent apikey and list id
        if (!($wiseAgent_key = $partners['wiseagent']['api_key'])) return;

        try {
            if(empty($lead->info('wiseagent_id'))) {
                $client_id = Partner_WiseAgent::addContact(
                    $wiseAgent_key,
                    $lead->info('email'),
                    $partners['wiseagent']['category'],
                    $partners['wiseagent']['call_list'],
                    $partners['wiseagent']['notify']
                );
                $lead->info('wiseagent_id', $client_id);
                $lead->save();
            } else {
                Partner_WiseAgent::updateContact(
                    $wiseAgent_key,
                    $lead->info('email'),
                    $partners['wiseagent']['category'],
                    $partners['wiseagent']['call_list'],
                    $partners['wiseagent']['notify']
                );
            }
        } catch (Exception $e) {
            return;
        }

    }
}
