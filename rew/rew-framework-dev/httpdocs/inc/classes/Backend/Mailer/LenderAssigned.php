<?php

/**
 * Backend_Mailer_LenderAssigned extends Backend_Mailer and is used for sending 'New Assign Lead' notifications to a Lender when a lead has been assigned to them.
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
 * $mailer = new Backend_Mailer_LenderAssigned(array(
 *     'leads' => array($lead)
 * ));
 *
 * // Add Recipient
 * $mailer->setRecipient('lender@example.com', 'Lender Name');
 *
 * // Send Email
 * $mailer->Send();
 *
 * ?>
 * </code>
 * @package Backend
 */
class Backend_Mailer_LenderAssigned extends Backend_Mailer
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

        // URL to Lender's Backend
        $url = Settings::getInstance()->URLS['URL_BACKEND'];

        // Generate Message
        $this->message  = '<p>Hello ' . Format::htmlspecialchars($recipient['name']) . ',</p>';
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
            if (!empty($lead['comments'])) {
                $this->message .= '<strong>User\'s Comments:</strong> ' . htmlspecialchars($lead['comments']) . '<br>';
            }
            if (!empty($lead['remarks'])) {
                $this->message .= '<strong>Agent\'s Remarks:</strong> ' . htmlspecialchars($lead['remarks']) . '<br>';
            }
            if (!empty($lead['notes'])) {
                $this->message .= '<strong>Quick Notes:</strong> ' . htmlspecialchars($lead['notes']) . '<br>';
            }
        }
        $this->message .= '<========================>';
        $this->message .= '<p><a href="' . $url . 'leads/?view=all&date=all">Click here</a> to view all your leads.</p>';
        $this->message .= '<p>Have a nice day!</p>';

        // Return Message
        return $this->message;
    }
}
