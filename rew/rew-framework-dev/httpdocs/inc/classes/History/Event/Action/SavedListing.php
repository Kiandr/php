<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Action_SavedListing extends History_Event_Action and is used for tracking when a Lead has saved a listing or an Agent or Associate has recommended a listing
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_SavedListing(array(
 *         'listing' => $listing // Listing Row
 *     ), array(
 *         new History_User_Lead(1)
 *         //new History_User_Agent(1)
 *     ));
 *
 *     $event->save();
 *
 * } catch (Exception $e) {
 *     echo '<p>' . $e->getMessage() . '</p>';
 * }
 * </code>
 *
 * @package History
 */
class History_Event_Action_SavedListing extends History_Event_Action implements History_IExpandable
{

    use History_Trait_HasListing;

    /**
     * @see History_Event::getMessage()
     */
    public function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'associate', 'lead')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if (in_array($type, array($user::TYPE_AGENT, $user::TYPE_ASSOCIATE))) {
                $agent = $user;
            }
            if ($type == $user::TYPE_LEAD) {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        // System History
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Recommended Listing to ' . $lead->displayLink() . ': ' . $this->getListingLink();
            } else {
                return $lead->displayLink() . ' Saved '.Locale::spell('Favorite').' Listing: ' . $this->getListingLink();
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Recommended Listing to ' . $lead->displayLink() . ': ' . $this->getListingLink();
        }

        // Lead History
        if ($options['view'] == 'lead') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Recommended Listing: ' . $this->getListingLink();
            } else {
                return 'Saved '.Locale::spell('Favorite').' Listing: ' . $this->getListingLink();
            }
        }
    }

    /**
     * @see History_IExpandable::getDetails()
     */
    public function getDetails()
    {
        return $this->getListingPreview();
    }

    /**
     * Increment lead's cached # of favorite listings
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {
        $db = is_null($db) ? $this->db : $db;
        if ($save = parent::save($db)) {
            foreach ($this->users as $user) {
                if ($user->getType() === $user::TYPE_LEAD) {
                    $query = $db->prepare("UPDATE `users` SET `num_favorites` = `num_favorites` + 1 WHERE `id` = ?;");
                    $query->execute(array($user->getUser()));
                }
            }
        }
        return $save;
    }
}
