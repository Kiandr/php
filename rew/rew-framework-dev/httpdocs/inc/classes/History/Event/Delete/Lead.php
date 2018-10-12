<?php

/**
 * History_Event_Delete_Lead extends History_Event_Delete and is used for tracking when an Agent has deleted a lead.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Delete_Lead(array(
 *         'lead' => array('id' => 1, 'first_name' => 'Lead', 'last_name' => 'Name')
 *     ), array(
 *         new History_User_Agent(1)
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
class History_Event_Delete_Lead extends History_Event_Delete
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'associate')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if ($type == $user::TYPE_AGENT) {
                $agent = $user;
            }
            if ($type == $user::TYPE_ASSOCIATE) {
                $agent = $user;
            }
        }

        // If Not Set, Make A Dummy Agent
        if (empty($agent)) {
            $agent = new History_User_Generic(0);
        }

        // Deleted Record
        $lead = $this->getData('row');

        // System Message
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' Deleted Lead: #' . $lead['id'] . ' - ' . $lead['first_name'] . ' ' . $lead['last_name'];
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Deleted Lead: #' . $lead['id'] . ' - ' . $lead['first_name'] . ' ' . $lead['last_name'];
        }
    }
}
