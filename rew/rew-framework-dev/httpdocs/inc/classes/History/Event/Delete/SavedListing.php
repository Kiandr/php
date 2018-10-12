<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Delete_SavedListing extends History_Event_Delete and is used for tracking when an Agent, Associate or Lead removed a saved listing.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Delete_SavedListing(array(
 *         'listing' => $listing, // Listing Row
 *         'recommended' => false // Is Recommendation
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
class History_Event_Delete_SavedListing extends History_Event_Delete implements History_IExpandable
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

        // Type of listing (recommended vs saved favorite)
        $recommended = $this->getData('recommended');
        $listing_type = !empty($recommended) ? 'Recommended' : Locale::spell('Favorite');

        // System History
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Removed ' . $lead->displayLink() . '\'s ' . $listing_type . ' Listing: ' . $this->getListingLink();
            } else {
                return $lead->displayLink() . ' Removed ' . $listing_type . ' Listing: ' . $this->getListingLink();
            }
        }

        // Agent/Associate HIstory
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Removed ' . $lead->displayLink() . '\'s ' . $listing_type . ' Listing: ' . $this->getListingLink();
        }

        // Head History
        if ($options['view'] == 'lead') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Removed '. $listing_type . ' Listing: ' . $this->getListingLink();
            } else {
                return 'Removed ' . $listing_type . ' Listing: ' . $this->getListingLink();
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
     * Decrease lead's cached # of favorite listings
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {
        $db = is_null($db) ? $this->db : $db;
        if ($save = parent::save($db)) {
            foreach ($this->users as $user) {
                if ($user->getType() === $user::TYPE_LEAD) {
                    $query = $db->prepare("UPDATE `users` SET `num_favorites` = IF(`num_favorites` > 0, `num_favorites` - 1, 0) WHERE `id` = ?;");
                    $query->execute(array($user->getUser()));
                }
            }
        }
        return $save;
    }
}
