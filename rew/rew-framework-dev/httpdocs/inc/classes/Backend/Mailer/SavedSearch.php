<?php

/**
 * Backend_Mailer_SavedSearch extends Backend_Mailer and is used for sending 'Saved Search' notifications to an Agent when a lead creates or edits a saved search
 * @package Backend
 */
class Backend_Mailer_SavedSearch extends Backend_Mailer
{

    /**
     * Get Email Subject to Send
     * @return string Email Subject
     */
    public function getSubject()
    {

        // Subject Already Set
        if (!empty($this->subject)) {
            return $this->subject;
        }

        // Previous Saved Search
        $updated = $this->data['updated'];

        // Default Subject
        return 'Saved Search Notification' . (!empty($updated) ? ' (Updated)' : '');
    }

    /**
     * Generate HTML Email Message to Send
     * @param array $tags Optional Tags for Replacement, {key} replaced with $tags['key]
     * @return string Email message to be sent
     * @uses Util_IDX::parseCriteria
     */
    public function getMessage(&$tags = array())
    {

        // Message Already Set
        if (!empty($this->message)) {
            return $this->message;
        }

        // Rejected Lead
        $lead = $this->data['lead'];
        $lead = new Backend_Lead($lead);
        $leadlink = $lead->getName();

        // Saved Search
        $search = $this->data['search'];

        // Previous Saved Search
        $updated = $this->data['updated'];

        // Search Criteria
        $criteria = unserialize($search['criteria']);

        // Search URL
        if ($criteria['search_by'] == 'map') {
            $search_url = Settings::getInstance()->URLS['URL_IDX_MAP'] . '?' . http_build_query($criteria);
        } else {
            $search_url = Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'] . '?' . http_build_query($criteria);
        }

        // Message Body (HTML)
        $this->message  = '<p>Hello ' . Format::htmlspecialchars($this->recipient['name']) . ',</p>' . PHP_EOL;

        // Search Updated
        if (!empty($updated)) {
            $this->message .= '<p>You have received this notification because <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a> has updated their saved search.</p>' . PHP_EOL;

        // Search Created
        } else {
            $this->message .= '<p>You have received this notification because <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead->getNameOrEmail()) . '</a> created a new saved search.</p>' . PHP_EOL;
        }

        // Lead Details
        $this->message .= '<========================>' . '<br>' . PHP_EOL;
        if (!empty($leadlink)) {
            $this->message .= '<strong>Name:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/summary/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a><br>' . PHP_EOL;
        }
        $this->message .= '<strong>Email:</strong> <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/email/?id=' . $lead['id'] . '">' . $lead['email'] . '</a><br>' . PHP_EOL;
        if (!empty($lead['phone'])) {
            $this->message .= '<strong>Phone:</strong> ' . $lead['phone'] . '<br>' . PHP_EOL;
        }
        $this->message .= '<strong>Search Title:</strong> <a href="' . $search_url . '">' . Format::htmlspecialchars($search['title']) . '</a><br>' . PHP_EOL;
        $this->message .= '<strong>Email Frequency:</strong> ' . ucwords($search['frequency']) . '<br>' . PHP_EOL;

        // Search Updated
        if (!empty($updated)) {
            // Old Search Criteria
            $this->message .= '<========================><br>' . PHP_EOL;
            $this->message .= '<strong>Old Search Criteria</strong><br>' . PHP_EOL;
            $this->message .= Util_IDX::parseCriteria(unserialize($updated['criteria']), $search['idx']);

            // New Search Criteria
            $this->message .= '<========================><br>' . PHP_EOL;
            $this->message .= '<strong>New Search Criteria</strong><br>' . PHP_EOL;
            $this->message .= Util_IDX::parseCriteria($criteria, $search['idx']);

        // Search Created
        } else {
            // Search Criteria
            $this->message .= '<========================><br>' . PHP_EOL;
            $this->message .= '<strong>Saved Search Criteria</strong><br>' . PHP_EOL;
            $this->message .= Util_IDX::parseCriteria($criteria, $search['idx']);
        }

        // Email Footer
        $this->message .= '<========================>' . PHP_EOL;
        $this->message .= '<p><a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '">Click here</a> to view all saved searches for ' . Format::htmlspecialchars($lead['first_name']) . '.</p>' . PHP_EOL;
        $this->message .= '<p><a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/edit/?id=' . $lead['id'] . '">Click here</a> to change notification settings.</p>' . PHP_EOL;
        $this->message .= '<p>Have a nice day!</p>';

        // Return Message
        return $this->message;
    }
}
