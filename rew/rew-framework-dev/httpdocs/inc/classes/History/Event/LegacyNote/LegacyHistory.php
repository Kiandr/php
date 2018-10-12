<?php
 
/**
* History_Event_LegacyNote_LegacyHistory
*
* <code>
* </code>
*
*/
 
class History_Event_LegacyNote_LegacyHistory extends History_Event_LegacyNote
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

        /* System History */
        if ($options['view'] == 'system') {
            return $lead->displayLink() . ' (' . $this->getData('type') . '): ' . $this->getData('details');
        }
 
        /* Lead History */
        if ($options['view'] == 'lead') {
            return $this->getData('type') . ': ' . $this->getData('details');
        }
    }
}
