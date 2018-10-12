<?php

/**
 * History_Event_Update_TeamAdd
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_TeamRemove extends History_Event_Update
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent')) ? $options['view'] : 'system';

        //Distinguish Deleting Agent from Primary Team Agent
        $agent_id = $this->getData('removing_agent');
        $secondary_agent_id = $this->getData('secondary_agent');

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if (in_array($type, array($user::TYPE_AGENT))) {
                if (isset($agent_id) && $user->getUser() == $agent_id) {
                    $agent = $user;
                }
                if (isset($secondary_agent_id) && $user->getUser() == $secondary_agent_id) {
                    $secondary_agent = $user;
                }
            }
        }


        // Deleted Record
        $team = $this->getData('team');
        $team_link = '#' . $team['id'] . ' - ' . $team['name'];

        // Sytem History
        if ($options['view'] == 'system') {
            if (!empty($agent)) {
                return $agent->displayLink() . ' Removed ' . $secondary_agent->displayLink() . ' from Team: ' . $team_link;
            } else {
                return $secondary_agent->displayLink() . ' Removed from Team: ' . $team_link;
            }
        }

        // Agent History
        if ($options['view'] == 'agent') {
            if ($agent_id == $secondary_agent_id) {
                return 'Added to Team: ' . $team_link;
            } else {
                if ($options['user'] == $agent_id) {
                    return 'Removed ' . $secondary_agent->displayLink() . ' from Team: ' . $team_link;
                } else {
                    return 'Removed from Team: ' . $team_link . (!empty($agent) ? ' by ' . $agent->displayLink() : '');
                }
            }
        }
    }
}
