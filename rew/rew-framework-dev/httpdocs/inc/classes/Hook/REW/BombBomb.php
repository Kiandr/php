<?php

/**
 * Hook_REW_BombBomb
 * Base class for BombBomb hooks
 *
 * @package Hooks
 */
class Hook_REW_BombBomb extends Hook {

    /**
     * Partner system instance
     * @var Partner_BombBomb
     */
    protected static $_instance;

    /**
     * BombBomb list id
     * @var int
     */
    protected static $listId;

    /**
     * Update Contact
     * @param Partner_BombBomb $bombBomb
     * @param int $bombBombId
     * @param Backend_Lead $lead
     * @param array $group
     */
    protected function addContact (Partner_BombBomb $bombBomb, $bombBombId, Backend_Lead $lead) {

        // Update BombBomb contact
        $bombBomb->addContact($lead->info('email'), [
            'firstname' 		=> $lead->info('first_name'),
            'lastname' 			=> $lead->info('last_name'),
            'phone_number' 		=> $lead->info('phone'),
            'address_line_1' 	=> $lead->info('address1'),
            'address_line_2' 	=> $lead->info('address2'),
            'city' 				=> $lead->info('city'),
            'state' 			=> $lead->info('state'),
            'country' 			=> $lead->info('country'),
            'postal_code' 		=> $lead->info('zip'),
            'comments' 			=> $lead->info('notes'),
        ], $bombBombId);

    }

	/**
	 * Get the BombBomb partner instance (if available)
	 * @param Backend_Agent $agent
	 * @return Partner_BombBomb|NULL
	 */
	protected function getPartner (Backend_Agent $agent) {

	    // Return existing instance
	    if (!is_null(self::$_instance)) return self::$_instance;

		// Require Partners
		$partners = json_decode($agent->info('partners'), true);
		if (empty($partners)) return null;

		// Require Bombbomb apikey and lsit id
		if (!($bombbomb_key = $partners['bombbomb']['api_key'])) return null;
		if (!($bombbomb_list_id = $partners['bombbomb']['list_id'])) return null;

		// Create instance
		$bombBomb = new Partner_BombBomb([
		    'api_key' => $bombbomb_key
		]);

		// Set List Id
		$this->setListId($bombbomb_list_id);

		// Cache instance & return
		self::$_instance = $bombBomb;
		return self::$_instance;
	}

	/**
	 * Get list id
	 * @return int|null
	 */
	protected function getListId() {
		return $this->listId;
	}

	/**
	 * Set list id
	 * @param unknown $listId
	 */
	protected function setListId($listId) {
		$this->listId = $listId;
	}
}
