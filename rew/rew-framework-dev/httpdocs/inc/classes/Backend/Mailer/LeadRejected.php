<?php

/**
 * Backend_Mailer_LeadRejected extends Backend_Mailer and is used for sending 'Lead Rejected' notifications to Super Admin when an Agent rejects a leads.
 *
 * Here is an example on how to use this class:
 * <code>
 * <?php
 *
 * // Agent Row
 * $agent = array(
 *     'id'         => 1,
 *     'first_name' => 'Super',
 *     'last_name'  => 'Admin',
 *     'email'      => 'admin@example.com'
 * );
 *
 * // Lead Row
 * $lead = array(
 *     'id'         => 1,
 *     'first_name' => 'Lead',
 *     'last_name'  => 'Name',
 *     'email'      => 'user@domain.com',
 *     'phone'      => '123-456-7890',
 *     'rejectwhy'  => 'Reason for rejection'
 * );
 *
 * // Setup Mailer
 * $mailer = new Backend_Mailer_LeadRejected(array(
 *     'agent' => $agent,
 *     'lead'  => $lead
 * ));
 *
 * // Add Recipient
 * $mailer->setRecipient('admin@example.com', 'Super Admin');
 *
 * // Send Email
 * $mailer->Send();
 *
 * ?>
 * </code>
 * @package Backend
 */
class Backend_Mailer_LeadRejected extends Backend_Mailer
{
    
    /**
     * Get Email Subject to Send
     *
     * @return string Email Subject
     */
    public function getSubject()
    {
        
        // Subject Already Set
        if (!empty($this->subject)) {
            return $this->subject;
        }
        
        // Default Subject
        return 'Lead Rejected';
    }
    
    /**
     * Generate HTML Email Message to Send
     *
     * @return string Email message to be sent
     */
    public function getMessage()
    {
        
        // Message Already Set
        if (!empty($this->message)) {
            return $this->message;
        }
        
        // Email Recipient
        $recipient = $this->recipient;
        
        // Rejected Agent
        $agent = $this->data['agent'];

        // Rejected Lead
        $lead = $this->data['lead'];
        $lead = new Backend_Lead($lead);

        // Generate Message
        $this->message = '<p>Hello ' . Format::htmlspecialchars($recipient['name']) . ',</p>';
        $this->message .= '<p><a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'agents/agent/summary/?id=' . $agent['id'] . '">' . Format::htmlspecialchars($agent['first_name'] . ' ' . $agent['last_name']) . '</a> has <strong>rejected</strong> the following lead:</p>';
        $this->message .= '<========================><br>';
        $this->message .= '<strong>Name:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a><br>';
        $this->message .= '<strong>Email:</strong> ' . $lead['email'] . '<br>';
        if (!empty($lead['phone'])) {
            $this->message .= '<strong>Phone:</strong> ' . $lead['phone'] . '<br>';
        }
        if (!empty($lead['rejectwhy'])) {
            $this->message .= '<strong>Reason:</strong> ' . htmlspecialchars($lead['rejectwhy']) . '<br>';
        }
        $this->message .= '<========================>';
        $this->message .= '<p>Have a nice day!</p>';

        // Return Message
        return $this->message;
    }
}
