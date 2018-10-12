<?php

/**
 * History_Event_Action_Verified extends History_Event_Action and is used for tracking when an Lead has verified their email address
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_Verified(null, array(
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
class History_Event_Action_Verified extends History_Event_Action
{
    function getMessage(array $options = array())
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
            return $lead->displayLink() . ' Verified Email Address';
        }

        /* Lead Message */
        if ($options['view'] == 'lead') {
            return 'Verified Email Address';
        }
    }
}
