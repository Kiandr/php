<?php

/**
 * Backend_Mailer_MessageSent
 * @package Backend_Mailer
 */
class Backend_Mailer_MessageSent extends Backend_Mailer_SMS
{

    /**
     * @var Backend_Agent
     */
    protected $_agent;

    /**
     * @var Backend_Lead
     */
    protected $_lead;

    /**
     * @see Backend_Mailer::__construct
     */
    public function __construct($data = array())
    {
        parent::__construct($data);

        // Set sender details
        $lead = $this->getLead();
        $this->setSender(Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'], $lead->getNameOrEmail());

        // Send to assigned agent
        $agent = $this->getAgent();
        $this->setRecipient($agent->getEmail(), $agent->getName());
    }

    /**
     * @see Backend_Mailer::getSubject
     */
    public function getSubject()
    {
        $subject = $this->data['subject'];
        return 'New Message: ' . $subject;
    }

    /**
     * @see Backend_Mailer::getMessage
     */
    public function getMessage()
    {
        $lead = $this->getLead();
        $lead_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/messages/?id=' . $lead->getId();
        $message  = '<p>You have received a new message from <a href="' . $lead_url . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a>:</p>';
        $message .= '<blockquote>' . nl2br(htmlspecialchars($this->data['message'])) . '</blockquote>';
        $message .= '<p>' . str_repeat('-', 50) . '</p>';
        $message .= '<p><a href="' . $lead_url . '">Click here respond to this message</a> or view any other messages you may have.</p>';
        return $message;
    }

    /**
     * @see Backend_Mailer_SMS::getSmsMessage
     */
    public function getSmsMessage()
    {
        $lead = $this->getLead();
        $lead_url = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/messages/?id=' . $lead->getId();
        return '* New message from ' . $lead->getNameOrEmail() . ' * ' . $lead_url;
    }

    /**
     * Get lead instance
     * @return Backend_Lead|NULL
     */
    protected function getLead()
    {
        if ($reload || !$this->_lead) {
            $user_id = $this->data['user_id'];
            if (empty($user_id)) {
                return null;
            }
            $this->_lead = Backend_Lead::load($user_id);
        }
        return $this->_lead;
    }

    /**
     * Get agent instance
     * @return Backend_Agent|NULL
     */
    protected function getAgent()
    {
        if ($reload || !$this->_agent) {
            $agent_id = $this->data['agent_id'];
            if (empty($agent_id)) {
                return null;
            }
            $this->_agent = Backend_Agent::load($agent_id);
        }
        return $this->_agent;
    }
}
