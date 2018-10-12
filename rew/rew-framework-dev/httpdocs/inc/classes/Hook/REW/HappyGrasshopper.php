<?php

/**
 * Hook_REW_HappyGrasshopper
 * Base class for HappyGrasshopper hooks
 *
 * @package Hooks
 */
class Hook_REW_HappyGrasshopper extends Hook {

    /**
     * Partner system instance
     * @var Partner_HappyGrasshopper
     */
    protected static $_instance;

	/**
	 * Get the HappyGrasshopper partner instance (if available)
	 * @param Backend_Agent $agent
	 * @return Partner_HappyGrasshopper|NULL
	 */
	protected function getPartner (Backend_Agent $agent) {

	    // Return existing instance
	    if (!is_null(self::$_instance)) return self::$_instance;

		// Require Partners
		$partners = json_decode($agent->info('partners'), true);
		if (empty($partners)) return null;

		// Require Bombbomb apikey and lsit id
		if (!($grasshopper_key = $partners['grasshopper']['api_key'])) return null;
		if (!($grasshopper_user_key = $partners['grasshopper']['user_key'])) return null;
		if (!($grasshopper_user_code = $partners['grasshopper']['user_code'])) return null;

		// Create instance
		$happyGrasshopper = new Partner_HappyGrasshopper([
		    'api_key' => $grasshopper_key,
		    'user_key' => $grasshopper_user_key,
		    'user_code' => $grasshopper_user_code
		]);
		return $happyGrasshopper;
	}

	/**
	 * Add a contact
	 * @param Partner_HappyGrasshopper $happyGrasshopper
	 * @param Backend_Lead $lead
	 * @param array $groupNames
	 */
	protected function addContact(Partner_HappyGrasshopper $happyGrasshopper, Backend_Lead $lead, array $groupNames) {

	    // Add Contact
	    $happyGrasshopper->addContact(
	        $lead->info('first_name'),
	        $lead->info('last_name'),
	        $lead->info('email'),
	        $groupNames,
	        $response
        );

	    // Set Data Id
	    if (!empty($response['DataID']) && $response['DataID'] != 0) {
	        $this->setDataId($lead, $response['DataID']);
	        return;
	    }

        // Contact exists in HappyGrasshopper, have to load HG contact list & update
        $ghContact = $happyGrasshopper->getContact($lead->info('email'));
        $this->setDataId($lead, $ghContact['DataID']);
        $dataId = $ghContact['DataID'];
        $happyGrasshopper->updateContact(
            $dataId,
            $lead->info('first_name'),
            $lead->info('last_name'),
            $lead->info('email'),
            $groupNames
        );

	}

	/**
	 * Get Happy Grasshopper Data Id
	 * @param Backend_Lead $lead
	 * @return int|null
	 */
	protected function getDataId(Backend_Lead $lead) {

	    $db = DB::get('users');
	    $query = $db->prepare("SELECT `happygrasshopper_data_id` FROM `users` WHERE `id` = :id LIMIT 1;");
	    $query->execute(['id' => $lead->getId()]);
	    return $query->fetchColumn();

	}

	/**
	 * Get Happy Grasshopper Data Id
	 * @param Backend_Lead $lead
	 * @param int $dataId
	 */
	protected function setDataId(Backend_Lead $lead, $dataId) {

	    try {
	        $db = DB::get('users');
	        $query = $db->prepare("UPDATE `users` SET `happygrasshopper_data_id` = :dataID WHERE `id` = :id;");
	        $query->execute([
	            'dataID' => $dataId,
	            'id' => $lead->getId()
	        ]);
	    } catch(PDOException $e) {}

	}

	/**
	 * Get System Group Names
	 * @return array
	 */
	protected function getSystemGroupNames() {

	    // System groups
	    $system_group_names = array();
	    if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_GRASSHOPPER'])) 	$system_group_names[] = Partner_HappyGrasshopper::GROUP_NAME;
	    if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_BOMBBOMB'])) 		$system_group_names[] = Partner_BombBomb::GROUP_NAME;
	    if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_WISEAGENT'])) 	$system_group_names[] = Partner_WiseAgent::GROUP_NAME;
	    return $system_group_names;

	}

	/**
	 * Get Non-System Group Names
	 * @param array
	 * @return array
	 */
	protected function getNonSystemGroupNames(array $groups) {

	    $systemGroupNames = $this->getSystemGroupNames();

	    $groupNames = [];
	    foreach ($groups AS $group) {
	        if (!in_array($group['name'], $systemGroupNames)) $groupNames[] = $group['name'];
	    }
	    return $groupNames;

	}
}
