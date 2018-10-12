<?php

/**
 * History_Event_Text_Outgoing
 * @package History
 */
class History_Event_Text_Outgoing extends History_Event_Text
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
                $message = ':agent sent text message to :lead at :to';
            } else {
                $message = 'Text message sent to :lead at :to';
            }

        // Viewing agent's history
        } else if (in_array($options['view'], array('agent', 'associate', 'lender'))) {
            $message = 'Text message sent to :lead at :to';

        // Viewing lead's history
        } else if ($options['view'] == 'lead') {
            if ($agent = $this->getAgent()) {
                $message = ':agent sent text message to :to';
            } else {
                $message = 'Text message was sent to :to';
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
        $html .= '<br><strong>Sent from:</strong> ' . $this->getFrom();
        $html .= '<br><strong>Sent message:</strong> ' . parent::getDetails();
        return $html;
    }

    /**
     * Strip ?uid param from URLs and convert URL and emails into hyperlinks
     * @return string
     */
    protected function getBody()
    {
        $html = parent::getBody();
        $formatted = new Format_HTML($html);
        $formatted->removeUrlParams(array('uid'));
        $formatted->convertUrlsAndEmailsToLinks();
        return $formatted->getOutput();
    }

    /**
     * Link phone numbers to send text form
     * @see History_Event_Text::getTo()
     */
    protected function getTo()
    {
        $lead = $this->getLead();
        if (empty($lead)) {
            return parent::getTo();
        }
        $from = preg_replace('/[^0-9]/', '', $this->getFrom());
        return implode(' and ', array_map(function ($number) use ($lead, $from) {
            $digits = preg_replace('/[^0-9]/', '', $number);
            return '<a href="' . URL_BACKEND . 'leads/lead/text/?id=' . $lead->getUser() . '&to=' . $digits . '&from=' . $from . '">' . $number . '</a>';
        }, $this->getNumbers('to')));
    }
}
