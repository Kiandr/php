<?php

/**
 * Backend_Mailer_MessageReply
 * @package Backend_Mailer
 */
class Backend_Mailer_MessageReply extends Backend_Mailer_MessageSent
{

    /**
     * @see Backend_Mailer::getSubject
     */
    public function getSubject()
    {
        $subject = $this->data['subject'];
        return 'RE: ' . $subject;
    }

    /**
     * @see Backend_Mailer::getMessage
     */
    public function getMessage()
    {
        $lead = $this->getLead();
        $lead_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/messages/?id=' . $lead->getId();
        $message  = '<p>You have received a reply from <a href="' . $lead_url . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a>:</p>';
        $message .= '<blockquote>' . nl2br(htmlspecialchars($this->data['message'])) . '</blockquote>';
        $message .= '<p>' . str_repeat('-', 50) . '</p>';
        $message .= '<p><a href="' . $lead_url . '">Click here respond to this message</a> or view any other messages you may have.</p>';
        return $message;
    }
}
