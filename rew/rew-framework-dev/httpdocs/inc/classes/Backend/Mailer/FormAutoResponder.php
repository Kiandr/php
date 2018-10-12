<?php

/**
 * Backend_Mailer_FormAutoResponder
 *
 */
class Backend_Mailer_FormAutoResponder extends Backend_Mailer
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

        // Auto-Responder Data
        $autoresponder = $this->data;

        // Require Auto-Responder
        if (empty($autoresponder)) {
            throw new Exception('No Auto-Responder Provided');
        }

        // Mailer Subject
        $this->setSubject($autoresponder['subject']);

        // Set as Plaintext Email..
        if ($autoresponder['is_html'] == 'false') {
            $autoresponder['document'] = '<p>' . nl2br($autoresponder['document']) . '</p>';
        }

        // Mailer Message
        $this->setMessage($autoresponder['document']);

        // Email Template
        if (!empty($autoresponder['tempid'])) {
            $this->setTemplate($autoresponder['tempid']);
        }
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

        // Auto-Responder Data
        $autoresponder = $this->data;

        // Require Auto-Responder
        if (empty($autoresponder)) {
            throw new Exception('No Auto-Responder Provided');
        }

        // Sender Information
        $autoresponder['from'] = in_array($autoresponder['from'], array('admin', 'agent', 'custom')) ? $autoresponder['from'] : 'agent';

        // User Database
        $db = DB::get('users');

        // Get Super Admin
        $sender = $db->fetch("SELECT `first_name`, `last_name`, `email`, `signature` FROM `agents` WHERE `id` = 1;");

        // Send from Super Admin
        if ($autoresponder['from'] == 'admin') {
            $this->setSender($sender['email'], $sender['first_name'] . ' ' . $sender['last_name']);

        // Send from Assigned Agent
        } elseif ($autoresponder['from'] == 'agent') {
            $sender = $db->fetch("SELECT `first_name`, `last_name`, `email`, `signature` FROM `agents` WHERE `id` = '" . $tags['agent'] . "';");
            $this->setSender($sender['email'], $sender['first_name'] . ' ' . $sender['last_name']);

        // Custom Sender
        } elseif ($autoresponder['from'] == 'custom') {
            $this->setSender($autoresponder['from_email'], $autoresponder['from_name']);
        }

        // Extra Tags
        $tags = array_merge(array(
            'signature'   => $sender['signature'],
        ), $tags);

        // Send Email
        $send = parent::Send($tags);
        if ($send) {
            // Require Lead ID
            if (!empty($tags['id'])) {
                // Log Event: Form Auto-Responder Sent to Lead
                $event = new History_Event_Email_AutoResponder(array(
                    'title'     => $auto_responder['title'],
                    'subject'   => $this->getSubject(),
                    'message'   => $this->getMessage(),
                    'tags'      => $this->getTags()
                ), array(
                    new History_User_Lead($tags['id'])
                ));

                // Save to DB
                $event->save();
            }
        }

        // Return Status
        return $send;
    }
}
