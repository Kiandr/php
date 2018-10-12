<?php

/**
 * Hook_REW_Zillow
 * Base class for Zillow hooks
 *
 * @package Hooks
 */
class Hook_REW_Zillow extends Hook
{

    /**
     * Partner system instance
     * @var Partner_Zillow
     */
    protected static $_instance;

    /**
     * Get the Zillow partner instance (if available)
     * @return Partner_Zillow|NULL
     */
    protected function getPartner($agent_id = null)
    {

        // Return existing instance
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }

        // Require add-on
        if (empty(Settings::getInstance()->MODULES['REW_PARTNERS_ZILLOW'])) {
            return null;
        }

        // Use Admin by Default
        if (!isset($agent_id)) {
            $agent_id = 1;
        }

        $db = !empty($db) ? $db : DB::get('users');

        // Fetch Agent
        $agent = $db->{'agents'}->getRow($agent_id);

        // Require agent
        if (empty($agent)) {
            return null;
        }

        // Require partner data
        if (empty($agent['partners'])) {
            return null;
        }

        // Require Partners
        $partners = json_decode($agent['partners'], true);
        if (empty($partners)) {
            return null;
        }

        // Require Zillow Key, Secret & User
        if (!($zillow_key = $partners['zillow']['key'])) {
            return null;
        }
        if (!($zillow_secret = $partners['zillow']['secret'])) {
            return null;
        }
        if (!($zillow_user = $partners['zillow']['id'])) {
            return null;
        }

        // Create instance
        $zillow = new Partner_Zillow([
            'user_id' => $zillow_user,
            'api_key' => $zillow_key,
            'api_secret' => $zillow_secret
        ]);

        // Require Valid Connection
        try {
            $account = $zillow->getAccount();
            if (!isset($account)) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        // Cache instance & return
        self::$_instance = $zillow;
        return self::$_instance;
    }
}
