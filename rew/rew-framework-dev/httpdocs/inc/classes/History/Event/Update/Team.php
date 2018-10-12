<?php

/**
 * History_Event_Update_Team
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_Team extends History_Event_Update
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent')) ? $options['view'] : 'system';

        // Field Changed
        $field = $this->getData('field');

        // History Event Users
        foreach ($this->users as $user) {
            if ($user->getType() == $user::TYPE_AGENT) {
                if (intval($user->getUser()) == intval($this->getData('updating_agent'))) {
                    $updating_agent = $user;
                }
                if (intval($user->getUser()) == intval($this->getData('primary_agent'))) {
                    $owning_agent = $user;
                }
                if ($field == 'agent_id') {
                    if (intval($user->getUser()) == intval($this->getData('old'))) {
                        $old_agent = $user;
                    }
                    if (intval($user->getUser()) == intval($this->getData('new'))) {
                        $new_agent = $user;
                    }
                }
            }
        }

        // Updated Team
        $team = $this->getData('team');
        $team_link = 'Team #' . $team['id'] . ' - ' . $team['name'];

        // Field Label
        $label = ucwords(strtolower(str_replace(array('-', '_'), ' ', $field)));

        if ($field == 'agent_id') {
            // Field Label
            $label = 'Primary Agent';

            // Change Values
            if (isset($old_agent)) {
                $old = $old_agent->displayLink();
            }
            if (isset($new_agent)) {
                $new = $new_agent->displayLink();
            }
        } else {
            // Field Label
            $label = ucwords(strtolower(str_replace(array('-', '_'), ' ', $field)));

            // Change Values
            $old = $this->getData('old');
            $new = $this->getData('new');
        }

        // Change Type
        $change = false;
        if (!empty($old) && !empty($new)) {
            $change = 'changed';
        } elseif (!empty($old) && empty($new)) {
            $change = 'removed';
        } elseif (!empty($new) && empty($old)) {
            $change = 'added';
        }

        // Sytem History
        if ($options['view'] == 'system') {
            switch ($change) {
                case 'changed':
                    return $team_link . '\'s ' . $label . ' Changed from "' . $old . '" to "' . $new . '"' . (!empty($updating_agent) ? ' by ' . $updating_agent->displayLink() : '');
                    break;
                case 'removed':
                    return $team_link . '\'s ' . $label . ' Removed' . (!empty($updating_agent) ? ' by ' . $updating_agent->displayLink() : '') . ': ' . $old;
                    break;
                case 'added':
                    return $team_link . '\'s ' . $label . ' Added as "' . $new . '"' . (!empty($updating_agent) ? ' by ' . $updating_agent->displayLink() : '');
                    break;
            }
        }

        if (!empty($updating_agent) && $this->getData('updating_agent') != $this->getData('primary_agent')) {
            $agent_link = $updating_agent->displayLink() . ' ';
        }

        // Agent History
        if ($options['view'] == 'agent') {
            switch ($change) {
                case 'changed':
                    return ucfirst($agent_link . 'changed ' . $team_link . '\'s ' . $label . ' from "' . $old . '" to "' . $new . '"');
                    break;
                case 'removed':
                    return ucfirst($agent_link . 'removed ' . $team_link . '\'s ' . $label . ': ' . $old);
                    break;
                case 'added':
                    return ucfirst($agent_link . 'added ' . $team_link . '\'s ' . $label . ' as "' . $new . '"');
                    break;
            }
        }
    }
}
