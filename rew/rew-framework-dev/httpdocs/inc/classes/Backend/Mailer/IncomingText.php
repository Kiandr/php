<?php

/**
 * Backend_Mailer_IncomingText
 */
class Backend_Mailer_IncomingText extends Backend_Mailer
{

    /**
     * Agent id
     * @var int
     */
    protected $agent_id = 1;

    /**
     * Lead ids
     * @var int[]
     */
    protected $lead_ids = array();

    /**
     * SMS message
     * @var string
     */
    protected $body;

    /**
     * SMS media URLs
     * @var array
     */
    protected $media;

    /**
     * SMS sent from
     * @var string
     */
    protected $from;

    /**
     * SMS sent to
     * @var string
     */
    protected $to;

    /**
     * Agent to receive this email
     * @var Backend_Agent
     */
    protected $_agent;

    /**
     * Leads assigned to phone number
     * @var Backend_Lead[]
     */
    protected $_leads;

    /**
     * @see Backend_Mailer::__construct
     */
    public function __construct($data = array())
    {

        // Setup mailer instance
        parent::__construct($data);

        // Text message details
        $props = array('agent_id', 'lead_ids', 'media', 'body', 'from', 'to');
        foreach ($props as $prop) {
            if (!empty($data[$prop])) {
                $this->$prop = $data[$prop];
            }
        }

        // Sent to assigned agent
        $agent = $this->getAgent();
        $this->setRecipient($agent->getEmail(), $agent->getName());
    }

    /**
     * @see Backend_Mailer::getSubject
     */
    public function getSubject()
    {
        $leads = $this->getLeads();
        $from = count($leads) === 1 ? $leads[0]->getNameOrEmail() : Format::phone($this->from);
        return 'You\'ve received a text message from ' . Format::htmlspecialchars($from);
    }

    /**
     * @see Backend_Mailer::getMessage
     */
    public function getMessage()
    {
        if (empty($this->message)) {
            // Message details
            $to = Format::phone($this->to);
            $from = Format::phone($this->from);
            $body = $this->getTextMessage();
            $agent = $this->getAgent();
            $leads = $this->getLeads();
            // Generate message
            $this->message = '<p>Hello ' . Format::htmlspecialchars($agent['first_name']) . ',</p>';
            $this->message .= '<p>You\'ve received a new text message from ' . $from . ':</p>';
            $this->message .= PHP_EOL . '<blockquote>' . $body . '</blockquote>';
            // List leads
            if (!empty($leads)) {
                $this->message .= '<strong>Sent from: </strong>' . implode(', ', array_map(function ($lead) {
                    $url_reply = Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/text/?id=%s&from=' . $this->to . '&to=' . $this->from;
                    return '<a href=" ' . sprintf($url_reply, $lead->getId()) . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a>';
                }, $leads)) . '<br>';
            }
            $this->message .= PHP_EOL . '<strong>Sent to:</strong> ' . $to;
            $this->message .= '<p>Have a nice day!</p>';
        }
        return $this->message;
    }

    /**
     * Get HTML to display SMS message
     * @return string
     */
    public function getTextMessage()
    {
        $html = '';
        // Message body
        $body = $this->body;
        if (!empty($body)) {
            $emoji = new Format_Emoji;
            $html .= $emoji->parse($body);
        }
        // Attached media
        $media = $this->media;
        if (!empty($media)) {
            $html .= '<br>';
            $media = is_array($media) ? $media : array($media);
            foreach ($media as $url) {
                if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                    $html .= PHP_EOL . '<a href="' . $url . '" target="_blank"><img src="' . $url . '" style="max-height: 100px;" alt="[MEDIA]"></a>';
                }
            }
        }
        return $html;
    }

    /**
     * Get agent record
     * @return Backend_Agent
     */
    private function getAgent()
    {
        if (!$this->_agent) {
            $agent_id = $this->agent_id;
            $this->_agent = Backend_Agent::load($agent_id);
        }
        return $this->_agent;
    }

    /**
     * Get lead records
     * @return Backend_Lead[]
     */
    private function getLeads()
    {
        if (!$this->_leads) {
            $lead_ids = array_unique($this->lead_ids);
            $this->_leads = array_filter(array_map(function ($lead_id) {
                return Backend_Lead::load($lead_id);
            }, $lead_ids));
        }
        return $this->_leads;
    }

    /**
     * Get lead names
     * @return string
     */
    private function getLeadNames()
    {
        return implode(' and ', array_map(function ($lead) {
            return $lead->getName();
        }, $this->getLeads()));
    }
}
