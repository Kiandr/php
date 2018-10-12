<?php

/**
 * History_Event_Update_OptIn
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_OptIn extends History_Event_Update
{
    function getMessage(array $options = array())
    {

        // Message View
        $options['view'] = in_array($options['view'], array('system', 'agent')) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($user->getUser() != $this->getData('admin') || empty($agent)) {
                $agent = $user;
            }
            if ($type == 'History_User_Agent' && $user->getUser() == $this->getData('admin')) {
                $admin = $user;
            }
        }

        // If Not Set, Make A Dummy Agent
        if (empty($agent)) {
            $agent = new History_User_Agent(0);
        }

        // Admin is Agent
        if (!empty($admin) && $admin->getUser() === $agent->getUser()) {
            unset($admin);
        }

        // System Message
        if ($options['view'] == 'system') {
            if (!empty($admin)) {
                return $agent->displayLink() . ' has been Opted-In to Auto-Assignment and Auto-Rotation by ' . $admin->displayLink();
            } else {
                return $agent->displayLink() . ' has Opted-In to Auto-Assignment and Auto-Rotation';
            }
        }

        // Agent Message
        if ($options['view'] == 'agent') {
            if (!empty($admin)) {
                if (!empty($options['user']) && !empty($options['user']) && $options['user'] == $admin->getUser()) {
                    return 'Opted-In ' . $agent->displayLink() . ' to Auto-Assignment and Auto-Rotation';
                } else {
                    return 'Opted-In to Auto-Assignment and Auto-Rotation by ' . $admin->displayLink();
                }
            } else {
                return 'Opted-In to Auto-Assignment and Auto-Rotation';
            }
        }
    }
}
