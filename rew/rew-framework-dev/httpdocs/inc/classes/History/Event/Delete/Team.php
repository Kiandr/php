<?php

/**
 * History_Event_Delete_Team extends History_Event_Delete and is used for tracking when a Team is deleted.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Delete_Team(array(
 *         'team' => array('id' => 1, 'name' => 'Team', 'members' => 6)
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
class History_Event_Delete_Team extends History_Event_Delete
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent', 'associate')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if (in_array($type, array($user::TYPE_AGENT))) {
                if (intval($user->getUser()) == intval($this->getData('deleting_agent'))) {
                    $deleting_agent = $user;
                }
                if (intval($user->getUser()) == intval($this->getData('primary_agent'))) {
                    $owning_agent = $user;
                }
            }
        }

        // Deleted Record
        $team = $this->getData('team');
        $team_link = '#' . $team['id'] . ' - ' . $team['name'];

        // System History
        if ($options['view'] == 'system') {
            return ((!empty($deleting_agent)) ? $deleting_agent->displayLink() . ' ' : '') . ' Deleted Team: ' . $team_link . ((!empty($owner) && $creating_id != $owner_id) ? ', with Primary Agent: ' . $owner->displayLink() : '');
        }

        // Agent/Associate/Lender History
        if ($options['view'] == 'agent') {
            if ($this->getData('deleting_agent') == $this->getData('primary_agent')) {
                return 'Deleted Team: ' . $team_link;
            } else {
                if ($options['user'] == $this->getData('deleting_agent')) {
                    return 'Deleted Team: ' . $team_link . (!empty($owner) ? ', with Primary Agent: ' . $owner->displayLink() : '');
                } else {
                    return ((!empty($agent)) ? $agent->displayLink() . ' ' : '') . 'Deleted Team: ' . $team_link;
                }
            }
        }
    }
}
