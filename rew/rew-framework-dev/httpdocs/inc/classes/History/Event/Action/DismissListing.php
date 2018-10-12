<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Action_DismissListing
 * @package History
 */
class History_Event_Action_DismissListing extends History_Event_Action implements History_IExpandable
{
    use History_Trait_HasListing;

    /**
     * @see History_Event::getMessage()
     */
    public function getMessage(array $options = array())
    {

        // History view
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        $lead = $this->getLead();

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        // Viewing system history
        if ($options['view'] == 'system') {
            return $lead->displayLink() . ' dismissed listing: ' . $this->getListingLink();
        }

        // Viewing lead's history
        if ($options['view'] == 'lead') {
            return 'Dismissed listing: ' . $this->getListingLink();
        }
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
     * Increment lead's cached # of dismissed listings
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {
        $db = is_null($db) ? $this->db : $db;
        if ($save = parent::save($db)) {
            $lead = $this->getLead();
            if (!empty($lead)) {
                $query = $db->prepare("UPDATE `users` SET `num_dismissed` = `num_dismissed` + 1 WHERE `id` = ?;");
                $query->execute(array($lead->getUser()));
            }
        }
        return $save;
    }
}
