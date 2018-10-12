<?php

/**
 * History_Event_Action_Unsubscribe extends History_Event_Action and is used for tracking when an Lead has unsubscribed from receiving emails.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_Unsubscribe(array(
 *         'unsubscribe' => array('opt_marketing', 'opt_searches'), // Unsubscribed from..
 *         'bounce' => false // True, if performed by Bounce Detector
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
class History_Event_Action_Unsubscribe extends History_Event_Action
{
    function getMessage(array $options = array())
    {

        /* Message View */
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        /* Unsubscribed From... */
        $lists = array();
        $unsubscribe = $this->getData('unsubscribe');
        if (is_array($unsubscribe)) {
            if (in_array('opt_marketing', $unsubscribe)) {
                $lists[] = 'Campaigns';
            }
            if (in_array('opt_searches', $unsubscribe)) {
                $lists[] = 'Searches';
            }
        }

        /* Bound Detector Email */
        $bounce = $this->getData('bounce');

        /* History Event Users */
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead  = new History_User_Lead(0);
        }

        /* System Message */
        if ($options['view'] == 'system') {
            return $lead->displayLink() . ' Unsubscribed' . (!empty($lists) ? ' from: ' . implode(' &amp; ', $lists) : '') . ($bounce ? ' by Bounce Detector' : '');
        }

        /* Lead Message */
        if ($options['view'] == 'lead') {
            return 'Unsubscribed' . (!empty($lists) ? ' from: ' . implode(' &amp; ', $lists) : '') . ($bounce ? ' by Bounce Detector' : '');
        }
    }
}
