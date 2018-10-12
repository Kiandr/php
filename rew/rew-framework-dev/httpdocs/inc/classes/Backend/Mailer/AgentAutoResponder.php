<?php

/**
 * Backend_Mailer_AgentAutoResponder extends Backend_Mailer and is used for sending the Agent Auto-Responder to a Lead after an Agent changes their status from Pending to Accepted.
 *
 * Here is an example on how to use this class:
 * <code>
 * <?php
 *
 * // Agent Row
 * $agent = array(
 *     'id'           => 1,
 *     'ar_subject'   => 'Email Subject',
 *     'ar_document'  => 'Email Message',
 *     'signature'    => 'Email Signature',
 *     'add_sig'      => 'Y',
 *     'ar_cc_email'  => null,
 *     'ar_bcc_email' => null,
 *     'ar_tempid'    => null
 * );
 *
 * // Setup Mailer
 * $mailer = new Backend_Mailer_AgentAutoResponder(array(
 *     'agent' => $agent
 * ));
 *
 * // Send from Agent
 * $mailer->setSender('agent@example.com', 'Agent Name');
 *
 * // Send to Lead
 * $mailer->setRecipient('user@domain.com', 'Lead Name');
 *
 * // Send Email
 * $mailer->Send(array(
 *     'id'         => 1,
 *     'first_name' => 'Lead',
 *     'last_name'  => 'Name',
 *     'email'      => 'user@domain.com'
 * ));
 *
 * ?>
 * </code>
 * @package Backend
 */
class Backend_Mailer_AgentAutoResponder extends Backend_Mailer
{

    /**
     * Setup Agent Auto-Responder
     *
     * @param array $data
     * @throws Exception If $data['agent'] is not set
     * @uses parent::__construct()
     */
    public function __construct($data = array())
    {

        // Call Original Contruct (to Setup Mailer)
        parent::__construct(array_merge($data, array(
            'cc_email' => $data['agent']['ar_cc_email'],
            'bcc_email' => $data['agent']['ar_bcc_email'],
            'signature' => $data['agent']['signature'],
            'append'    => ($data['agent']['add_sig'] == 'Y')
        )));

        // Agent Data
        $agent = $this->data['agent'];

        // Require Agent
        if (empty($agent)) {
            throw new Exception('No Agent Provided');
        }

        // Mailer Subject
        $this->setSubject($agent['ar_subject']);

        // Set as Plaintext Email..
        if ($agent['ar_is_html'] == 'false') {
            $agent['ar_document'] = '<p>' . nl2br($agent['ar_document']) . '</p>';
        }

        // Mailer Message
        $this->setMessage($agent['ar_document']);

        // Email Template
        if (!empty($agent['ar_tempid'])) {
            $this->setTemplate($agent['ar_tempid']);
        }
    }

    /**
     * Send Agent Auto-Responder
     *
     * @param array $tags Optional Tags for Replacement, {key} replaced with $tags['key]
     * @return bool
     * @uses parent::Send()
     * @uses History_Event_Email_AutoResponder
     */
    public function Send($tags = array())
    {
        $send = parent::Send($tags);
        if ($send) {
            // Require Lead ID
            if (!empty($tags['id'])) {
                // Log Event: Agent Auto-Responder Sent to Lead
                $event = new History_Event_Email_AutoResponder(array(
                    'subject'   => $this->getSubject(),
                    'message'   => $this->getMessage(),
                    'tags'      => $this->getTags()
                ), array(
                    new History_User_Lead($tags['id']),
                    new History_User_Agent($this->data['agent']['id'])
                ));

                // Save to DB
                $event->save();
            }
        }
        return $send;
    }
}
