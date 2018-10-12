<?php

/**
 * History_Event_Text_OptOut
 * @package History
 */
class History_Event_Text_OptOut extends History_Event_Text_Incoming
{

    /**
     * @see History_Event::getMessage
     */
    function getMessage(array $options = array())
    {

        // Message view
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

        // Viewing system history
        if ($options['view'] == 'system') {
            if ($agent = $this->getAgent()) {
                $message = ':agent received opt-out message from :lead at :from';
            } else {
                $message = 'Opt-out message received from :lead at :from';
            }

        // Viewing agent's history
        } else if ($options['view'] == 'agent') {
            $message = 'Opt-out message received from :lead';

        // Viewing lead's history
        } else if ($options['view'] == 'lead') {
            if ($agent = $this->getAgent()) {
                $message = ':agent received opt-out message from :from';
            } else {
                $message = 'Opt-out message was received from :from';
            }
        }

        // Return formatted message
        return $this->formatMessage($message);
    }
}
