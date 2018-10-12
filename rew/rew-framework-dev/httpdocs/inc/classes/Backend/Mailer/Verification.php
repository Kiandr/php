<?php

/**
 * Backend_Mailer_Verification
 */
class Backend_Mailer_Verification extends Backend_Mailer
{

    /**
     * Setup Mailer
     *
     * @param array $data
     */
    public function __construct($data = array())
    {

        // Call Original Contruct (to Setup Mailer)
        parent::__construct($data);

        // Require lead
        $lead = $this->data['lead'];
        if (empty($lead)) {
            throw new Exception('No lead Provided');
        }

        // DB Connection
        $db = DB::get('users');

        // Get Super Admin
        $agent = $db->fetch("SELECT `first_name`, `last_name`, `email`, `signature`, `add_sig` FROM `agents` WHERE `id` = '1' LIMIT 1;");

        // Mailer Sender
        $this->setSender($agent['email'], $agent['first_name'] . ' ' . $agent['last_name']);

        // Add Recipient
        $this->setRecipient($lead['email'], $lead['first_name'] . ' ' . $lead['last_name']);

        // Agent's Signature
        $this->setSignature($agent['signature']);

        // Append Signature
        $this->appendSignature($agent['add_sig'] == 'Y');
    }

    /**
     * Get Email Subject to Send
     *
     * @return string Email Subject
     */
    public function getSubject()
    {
        if (!empty($this->subject)) {
            return $this->subject;
        }
        return 'Email Verification Code for ' . Http_Host::getDomain();
    }

    /**
     * Generate HTML Email Message to Send
     *
     * @return string Email message to be sent
     */
    public function getMessage(&$tags = array())
    {

        // Message Already Set
        if (!empty($this->message)) {
            return $this->message;
        }
        $signature = $this->getSignature();

        // Lead
        $lead = $this->data['lead'];

        // Verification Code
        $code = Format::toGuid($lead['guid']);

        // Verification URL
        $url = sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], $code);

        // Email Tags
        $tags = array_merge($tags, array(
            'first_name' => (!empty($lead['first_name'])) ? Format::htmlspecialchars($lead['first_name']) : 'Visitor',
            'code'          => $code,
            'verify_url'    => $url,
            'domain'        => Http_Host::getDomain(),
            'url'           => Settings::getInstance()->SETTINGS['URL'],
            'signature'     => $signature
        ));

        // Message Body (HTML)
        $this->message  = '<p>Dear {first_name},</p>' . PHP_EOL;
        $this->message .= '<p>Thank you for registering at <a href="{url}">{domain}</a>. Please take just one more step and verify your email address by clicking on the link below (or copy and paste the URL into your browser):</p>' . PHP_EOL;
        $this->message .= '<p><a href="{verify_url}" target="_blank">{verify_url}</a></p>' . PHP_EOL;
        $this->message .= '<p>Is your verification link not working? You can copy and paste this verification code as well.</p>' . PHP_EOL;
        $this->message .= '<p><strong>Your verification code is:</strong> {code}<br /></p>' . PHP_EOL;
        $this->message .= '<p>If you have any questions about our website, please don\'t hesitate to contact us.</p>' . PHP_EOL;

        // Append Signature
        if (!empty($signature) && !empty($this->append)) {
            $this->message .= '<p>{signature}</p>' . PHP_EOL;
        }

        // Return Message
        return $this->message;
    }

    /**
     * Send Email
     *
     * @param array $tags Optional Tags for Replacement
     * @return bool
     * @uses \PHPMailer\RewMailer::Send
     */
    public function Send($tags = array())
    {
        $send = parent::Send($tags);
        if ($send) {
            $lead = $this->data['lead'];

            // Require Lead ID
            if (!empty($lead['id'])) {
                // Log Event: Agent Auto-Responder Sent to Lead
                $event = new History_Event_Email_Verification(array(
                    'subject'   => $this->getSubject(),
                    'message'   => $this->getMessage(),
                    'tags'      => $this->getTags()
                ), array(
                    new History_User_Lead($lead['id'])
                ));

                // Save to DB
                $event->save();
            }
        }
        return $send;
    }
}
