<?php

/**
 * Backend_Mailer_AgentUnAssigned
 * @package Backend
 */
class Backend_Mailer_AgentUnAssigned extends Backend_Mailer
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
        return 'Un-Assigned from ' . number_format($count) . ' ' . Format::plural($count, 'Leads', 'Lead');
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

        // Generate Message
        $this->message  = '<p>Hello ' . Format::htmlspecialchars($recipient['name']) . ',</p>';
        $this->message .= '<p><strong>You have been un-assigned from the following ' . Format::plural(count($this->data['leads']), 'leads', 'lead') . ':</strong></p>';
        foreach ($this->data['leads'] as $lead) {
            $leadlink = Format::trim($lead['first_name'] . ' ' . $lead['last_name']);

            // Lead Details
            $this->message .= '<========================><br>' . PHP_EOL;
            if (!empty($leadlink)) {
                $this->message .= '<strong>Name:</strong> ' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '<br>';
            }
            if (empty($leadlink)) {
                $this->message .= '<strong>Email:</strong> ' . $lead['email'] . '<br>';
            }
            $this->message .= '<strong>Assigned:</strong> ' . Format::dateRelative($lead['timestamp_assigned']) . '<br>';
        }
        $this->message .= '<========================>';
        $this->message .= '<p>Have a nice day!</p>';

        // Return Message
        return $this->message;
    }
}
