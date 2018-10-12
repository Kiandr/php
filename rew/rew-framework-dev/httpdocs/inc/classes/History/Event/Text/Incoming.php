<?php

/**
 * History_Event_Text_Incoming
 * @package History
 */
class History_Event_Text_Incoming extends History_Event_Text
{

    /**
     * @see History_Event::getMessage()
     */
    public function getMessage(array $options = array())
    {

        // Message view
        $options['view'] = in_array($options['view'], array('system', 'agent', 'lead')) ? $options['view'] : 'system';

        // Viewing system history
        if ($options['view'] == 'system') {
            if ($agent = $this->getAgent()) {
                $message = ':agent received text message from :lead at :from';
            } else {
                $message = 'Text message received from :lead at :from';
            }

        // Viewing agent's history
        } else if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            $message = 'Text message received from :lead at :from';

        // Viewing lead's history
        } else if ($options['view'] == 'lead') {
            if ($agent = $this->getAgent()) {
                $message = ':agent received text message from :from';
            } else {
                $message = 'Text message was received from :from';
            }
        }

        // Return formatted message
        return $this->formatMessage($message);
    }

    /**
     * @see History_Event_Text::getDetails()
     */
    public function getDetails()
    {
        $html = '<strong>Sent to:</strong> ' . $this->getTo();
        $html .= '<br><strong>Received from:</strong> ' . $this->getFrom();
        $html .= '<br><strong>Received message:</strong> ' . parent::getDetails();
        return $html;
    }

    /**
     * Link phone numbers to send text form
     * @see History_Event_Text::getTo()
     */
    protected function getFrom()
    {
        $lead = $this->getLead();
        $from = parent::getFrom();
        if (empty($lead)) {
            return $from;
        }
        $digits = preg_replace('/[^0-9]/', '', $from);
        $to = preg_replace('/[^0-9]/', '', $this->getTo());
        return '<a href="' . URL_BACKEND . 'leads/lead/text/?id=' . $lead->getUser() . '&from=' . $to . '&to=' . $digits . '">' . $from . '</a>';
    }
}
