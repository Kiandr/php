<?php

/**
 * History_Event_Update_Lead
 *
 * <code>
 * </code>
 *
 */
class History_Event_Update_Lead extends History_Event_Update
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
            if (in_array($type, array($user::TYPE_AGENT, $user::TYPE_ASSOCIATE, $user::TYPE_LENDER))) {
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

        // Field Changed
        $field = $this->getData('field');

        // Field Label
        if($field == 'phone') {
            $label = 'Primary Phone';
        } else if($field == 'phone_cell') {
            $label = 'Secondary Phone';
        } else if($field == 'phone_home_status') {
            $label = 'Primary Phone Status';
        } else if($field == 'phone_cell_status') {
            $label = 'Secondary Phone Status';
        } else {
            $label = ucwords(strtolower(str_replace(array('-', '_'), ' ', $field)));
        }

        // Change Values
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

        // Format Values
        switch ($field) {
            // Currency Amounts
            case 'search_minimum_price':
            case 'search_maximum_price':
                $old = '$' . (!empty($old) ? number_format($old) : 0);
                $new = '$' . (!empty($new) ? number_format($new) : 0);
                break;
            // Phone # Status
            case 'phone_home_status':
            case 'phone_cell_status':
            case 'phone_work_status':
                $old = Backend_Lead::$phone_status[$old];
                $new = Backend_Lead::$phone_status[$new];
                break;
        }

        // System History
        if ($options['view'] == 'system') {
            switch ($change) {
                case 'changed':
                    return $lead->displayLink() . '\'s ' . $label . ' Changed from "' . $old . '" to "' . $new . '" by ' . $agent->displayLink();
                    break;
                case 'removed':
                    return $lead->displayLink() . '\'s ' . $label . ' Removed by ' . $agent->displayLink() . ': ' . $old;
                    break;
                case 'added':
                    return $lead->displayLink() . '\'s ' . $label . ' Added as "' . $new . '" by ' . $agent->displayLink();
                    break;
            }
        }

        // Agent/Associate History
        if (in_array($options['view'], array('agent', 'associate'))) {
            switch ($change) {
                case 'changed':
                    return 'Changed ' . $lead->displayLink() . '\'s ' . $label . ' from "' . $old . '" to "' . $new . '"';
                    break;
                case 'removed':
                    return 'Removed ' . $lead->displayLink() . '\'s ' . $label . ': ' . $old;
                    break;
                case 'added':
                    return 'Added ' . $lead->displayLink() . '\'s ' . $label . ' as "' . $new . '"';
                    break;
            }
        }

        // Lead History
        if ($options['view'] == 'lead') {
            switch ($change) {
                case 'changed':
                    return $label . ' Changed from "' . $old . '" to "' . $new . '" by ' . $agent->displayLink();
                    break;
                case 'removed':
                    return  $label . ' Removed by ' . $agent->displayLink() . ': ' . $old;
                    break;
                case 'added':
                    return $label . ' Added as "' . $new . '" by ' . $agent->displayLink();
                    break;
            }
        }
    }
}
