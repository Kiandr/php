<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Delete_DismissListing
 * @package History
 */
class History_Event_Delete_DismissListing extends History_Event_Delete implements History_IExpandable
{
    use History_Trait_HasListing;

    /**
     * @see History_Event::getMessage()
     */
    public function getMessage(array $options = array())
    {

        // History view
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

        // Involved users
        $lead = $this->getLead();
        $agent = $this->getAgent();

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        // Viewing system history
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' removed ' . $lead->displayLink() . '\'s dismissed listing: ' . $this->getListingLink();
            }
            return $lead->displayLink() . ' removed dismissed listing: ' . $this->getListingLink();
        }

        // Viewing agent's history
        if ($options['view'] == 'agent') {
            return 'Removed ' . $lead->displayLink() . '\'s dismissed listing: ' . $this->getListingLink();
        }

        // Viewing lead's history
        if ($options['view'] == 'lead') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' removed dismissed listing: ' . $this->getListingLink();
            }
            return 'Removed dismissed listing: ' . $this->getListingLink();
        }
    }

    /**
     * Get involved agent
     * @return History_User_Agent|NULL
     */
    public function getAgent()
    {
        foreach ($this->users as $user) {
            $type = $user->getType();
            if ($type == $user::TYPE_AGENT) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Get involved lead
     * @return History_User_Lead|NULL
     */
    public function getLead()
    {
        foreach ($this->users as $user) {
            $type = $user->getType();
            if ($type == $user::TYPE_LEAD) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @see History_IExpandable::getDetails()
     */
    public function getDetails()
    {
        return $this->getListingPreview();
    }

    /**
     * Decrease lead's cached # of dismissed listings
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {
        $db = is_null($db) ? $this->db : $db;
        if ($save = parent::save($db)) {
            $lead = $this->getLead();
            if (!empty($lead)) {
                $query = $db->prepare("UPDATE `users` SET `num_dismissed` = IF(`num_dismissed` > 0, `num_dismissed` - 1, 0) WHERE `id` = ?;");
                $query->execute(array($lead->getUser()));
            }
        }
        return $save;
    }
}
