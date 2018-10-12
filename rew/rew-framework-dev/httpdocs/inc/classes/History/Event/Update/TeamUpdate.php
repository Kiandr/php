<?php

/**
 * History_Event_Update_TeamAdd
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_TeamUpdate extends History_Event_Update
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent')) ? $options['view'] : 'system';

        //Distinguish Deleting Agent from Primary Team Agent
        $agent_id = $this->getData('updating_agent');
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
                return $agent->displayLink() . ' Updated ' . $secondary_agent->displayLink() . ' permissions for Team: ' . $team_link;
            } else {
                return $secondary_agent->displayLink() . ' Updated permissions for Team: ' . $team_link;
            }
        }

        // Agent History
        if ($options['view'] == 'agent') {
            if ($agent_id == $secondary_agent_id) {
                return 'Updated permissions for Team: ' . $team_link;
            } else {
                if ($options['user'] == $agent_id) {
                    return 'Updated ' . $secondary_agent->displayLink() . '\'s permissions for Team: ' . $team_link;
                } else {
                    return 'Had permissions updated Team: ' . $team_link . (!empty($agent) ? ' by ' . $agent->displayLink() : '');
                }
            }
        }
    }

    /**
     * Get Agent Permissions
     * @see History_IExpandable::getDetails()
     */
    function getDetails()
    {

        // Agent Permisisons
        $output = '';

        // Permissions Agent is Granted
        $granted_permissions = Format::unserialize($this->getData('granted_permissions'));
        if (!empty($granted_permissions)) {
            foreach ($granted_permissions as $permission => $value) {
                $output .= '<strong>Granted Permissions<br>' . PHP_EOL;
                $label = ucwords(strtolower(str_replace(array('-', '_'), ' ', $permission)));
                $output .= '<strong>' . $label. ':</strong> ' . $value . '<br>' . PHP_EOL;
            }
        }

        // Permissions Agent is Granting
        $granting_permissions = Format::unserialize($this->getData('granting_permissions'));
        if (!empty($granting_permissions)) {
            foreach ($granting_permissions as $permission => $value) {
                $output .= '<strong>Granting Permissions<br>' . PHP_EOL;
                $label = ucwords(strtolower(str_replace(array('-', '_'), ' ', $permission)));
                $output .= '<strong>' . $label. ':</strong> ' . $value . '<br>' . PHP_EOL;
            }
        }

        // Parse Search Criteria
        return $output;
    }
}
