<?php

/**
 * History_Event_Update_Status
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_Status extends History_Event_Update
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

        // Status Values
        $old = $this->getData('old');
        $new = $this->getData('new');

        // Change Type
        $change = false;
        if (!empty($old) && !empty($new)) {
            $change = 'changed';
        } elseif (!empty($old) && empty($new)) {
            $change = 'removed';
        } elseif (!empty($new) && empty($old)) {
            $change = 'added';
        }

        // System History
        if ($options['view'] == 'system') {
            switch ($change) {
                case 'changed':
                    return $lead->displayLink() . '\'s Status Changed from "' . $old . '" to "' . $new . '" by ' . $agent->displayLink();
                    break;
                case 'removed':
                    return $lead->displayLink() . '\'s Status Removed by ' . $agent->displayLink() . ': ' . $old;
                    break;
                case 'added':
                    return $lead->displayLink() . '\'s Status Set as "' . $new . '" by ' . $agent->displayLink();
                    break;
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            switch ($change) {
                case 'changed':
                    return 'Changed ' . $lead->displayLink() . '\'s Status from "' . $old . '" to "' . $new . '"';
                    break;
                case 'removed':
                    return 'Removed ' . $lead->displayLink() . '\'s Status: ' . $old;
                    break;
                case 'added':
                    return 'Set ' . $lead->displayLink() . '\'s Status as "' . $new . '"';
                    break;
            }
        }

        // Lead History
        if ($options['view'] == 'lead') {
            switch ($change) {
                case 'changed':
                    return 'Status Changed from "' . $old . '" to "' . $new . '" by ' . $agent->displayLink();
                    break;
                case 'removed':
                    return 'Status Removed by ' . $agent->displayLink() . ': ' . $old;
                    break;
                case 'added':
                    return 'Status Set as "' . $new . '" by ' . $agent->displayLink();
                    break;
            }
        }
    }
}
