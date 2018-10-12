<?php

/**
 * History_Event_Action_ViewedListing extends History_Event_Action and is used for tracking when a Lead has viewed a listing
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_ViewedListing(array(
 *         'listing' => $listing // Listing Row
 *     ), array(
 *         new History_User_Lead(1)
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
class History_Event_Action_ViewedListing extends History_Event_Action implements History_IExpandable
{

    use History_Trait_HasListing;

    /* Basic Message */
    public function getMessage(array $options = array())
    {

        /* Message View */
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        /* History Event Users */
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        /* System Message */
        if ($options['view'] == 'system') {
            return $lead->displayLink() . ' Viewed Listing: ' . $this->getListingLink();
        }

        /* Lead Message */
        if ($options['view'] == 'lead') {
            return 'Viewed Listing: ' . $this->getListingLink();
        }
    }

    /**
     * @see History_IExpandable::getDetails()
     */
    public function getDetails()
    {
        return $this->getListingPreview();
    }
}
