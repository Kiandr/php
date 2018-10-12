<?php

/**
 * History_Event_Create_LeadReminder extends History_Event_Create and is used for tracking when an Agent creates a lead reminder.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Create_LeadReminder(array(
 *         'timestamp' => time(),
 *         'details'   => 'Meeting'
 *         'type'      => 'Reminder details here'
 *     ), array(
 *         new History_User_Agent(1),
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
class History_Event_Create_LeadReminder extends History_Event_Create
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
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

        // If Not Set, Make A Dummy Lead/Agent
        if (empty($agent)) {
            $agent = new History_User_Generic(0);
        }
        if (empty($lead)) {
            $lead  = new History_User_Lead(0);
        }

        // Date Format
        $format = 'F\, jS Y \a\t g\:ia';

        // Reminder Date
        $date = date($format, $this->getData('timestamp'));

        // Reminder Type
        $type = $this->getData('type');

        // Lookup Type
        if (is_numeric($type)) {
            $type = $this->db->calendar_types->getRow($type);
            if (!empty($type)) {
                $type = $type['title'];
            }
        }

        // Format Type
        $type = !empty($type) ? ' (' . $type . ')' : '';

        // System History
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' added Reminder for ' . $lead->displayLink() . ' on ' . $date . $type . ': ' . $this->getData('details');
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            return 'Reminder added for ' . $lead->displayLink() . ' on ' . $date . $type . ': ' . $this->getData('details');
        }

        // Lead History
        if ($options['view'] == 'lead') {
            return 'Reminder added by ' . $agent->displayLink() . ' for ' . $date . $type . ': ' . $this->getData('details');
        }
    }
}
