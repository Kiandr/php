<?php

/**
 * Backend_Mailer_AgentAssigned extends Backend_Mailer and is used for sending 'New Assign Lead' notifications to an Agent when a lead has been assigned to them.
 *
 * Here is an example on how to use this class:
 * <code>
 * <?php
 *
 * // Lead Row
 * $lead = array(
 *     'id'         => 1,
 *     'first_name' => 'Lead',
 *     'last_name'  => 'Name',
 *     'email'      => 'user@domain.com',
 *     'phone'      => '123-456-7890',
 *     'status'     => 'pending',
 *     'comments'   => 'Users Comments',
 *     'notes'      => 'Quick Notes',
 *     'remarks'    => 'Agent Remarks'
 * );
 *
 * // Setup Mailer
 * $mailer = new Backend_Mailer_AgentAssigned(array(
 *     'leads' => array($lead)
 * ));
 *
 * // Add Recipient
 * $mailer->setRecipient('agent@example.com', 'Agent Name');
 *
 * // Send Email
 * $mailer->Send();
 *
 * ?>
 * </code>
 * @package Backend
 */
class Backend_Mailer_AgentAssigned extends Backend_Mailer_SMS
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

        // Lead Count
        $count = count($this->data['leads']);

        // Default Subject
        return 'You have ' . number_format($count) . ' new ' . Format::plural($count, 'leads', 'lead') . '!';
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

        // Lead Count
        $count = count($this->data['leads']);

        // Generate Message
        $this->message  = '<p>Hello ' . $recipient['name'] . ',</p>';
        $this->message .= '<p><strong>You have been assigned to the following ' . Format::plural($count, 'leads', 'lead') . ':</strong></p>';
        foreach ($this->data['leads'] as $lead) {
            $leadlink = Format::trim($lead['first_name'] . ' ' . $lead['last_name']);

            $this->message .= '<========================><br>' . PHP_EOL;
            if (!empty($leadlink)) {
                $this->message .= '<strong>Name:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a><br>';
            }
            if (!empty($leadlink)) {
                $this->message .= '<strong>Email:</strong> ' . $lead['email'] . '<br>';
            }
            if (empty($leadlink)) {
                $this->message .= '<strong>Email:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . $lead['email'] . '</a><br>';
            }
            if (!empty($lead['phone'])) {
                $this->message .= '<strong>Phone:</strong> ' . $lead['phone'] . '<br>';
            }
            if (!empty($lead['status'])) {
                $this->message .= '<strong>Status:</strong> ' . $lead['status'] . '<br>';
            }
            if (!empty($lead['comments'])) {
                $this->message .= '<strong>User\'s Comments:</strong> ' . htmlspecialchars($lead['comments']) . '<br>';
            }
            if (!empty($lead['remarks'])) {
                $this->message .= '<strong>Agent\'s Remarks:</strong> ' . htmlspecialchars($lead['remarks']) . '<br>';
            }
            if (!empty($lead['notes'])) {
                $this->message .= '<strong>Quick Notes:</strong> ' . htmlspecialchars($lead['notes']) . '<br>';
            }
            if (!empty($lead['source_app_id'])) {
                if ($api_application = Backend_Lead::apiSource($lead['source_app_id'])) {
                    $this->message .= '<strong>API Source:</strong> ' . htmlspecialchars($api_application['name']) . '<br>';
                }
            }
        }
        $this->message .= '<========================>';
        $this->message .= '<p><strong>Don\'t forget to accept ' . Format::plural($count, 'these new leads', 'this new lead') . '.</strong></p>';
        $this->message .= '<p><a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/?view=all&date=all&status=pending">Click here</a> to view all your pending leads.</p>';
        $this->message .= '<p>Have a nice day!</p>';

        // Return Message
        return $this->message;
    }

    /**
     * Generate SMS Message (Plaintext)
     * @see Backend_Mailer_SMS::getSmsMessage()
     */
    public function getSmsMessage()
    {

        // SMS Message Already Set
        if (!empty($this->sms_message)) {
            return $this->sms_message;
        }

        // Assigned Leads
        $leads = $this->data['leads'];

        // Lead Count
        $count = count($leads);

        // Generate SMS Message
        $this->sms_message = '*You have ' . Format::plural($count, number_format($count) . ' new leads', 'a new lead') . '*' . PHP_EOL;
        if ($count > 1) {
            $this->sms_message .= 'View pending leads: ' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/?view=my-leads&date=all&status=pending';
        } else {
            foreach ($leads as $lead) {
                $this->sms_message .= $lead['first_name'] . ' ' . $lead['last_name'] . (!empty($lead['phone']) ? ' ' . $lead['phone'] : '')
                    . PHP_EOL . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'];
            }
        }

        // Return SMS Message
        return $this->sms_message;
    }
}
