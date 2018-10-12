<?php

/**
 * Backend_Mailer_SavedSearchSuggestion
 *
 * @package Backend
 */
class Backend_Mailer_SavedSearchSuggestion extends Backend_Mailer
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
        return 'Customize your search alerts - Never miss a hot deal';
    }

    /**
     * Generate HTML Email Message to Send
     *
     * @param array Tags
     * @return string Email message to be sent
     */
    public function getMessage(&$tags = array())
    {

        // Message Already Set
        if (!empty($this->message)) {
            return $this->message;
        }
        $signature = $this->getSignature();
        $unsubscribe = $this->getUnsubscribe();

        // Lead
        $lead = $this->data['lead'];

        // Search Criteria
        $criteria = $this->data['criteria'];
        $criteria = is_array($criteria) ? $criteria : array();

        // UID for Auto-Login
        $uid = Format::toGuid($lead['guid']);

        // Site URL
        $url = !empty($this->data['url']) ? $this->data['url'] : Settings::getInstance()->SETTINGS['URL'];

        // Email Tags
        $tags = array_merge($tags, array(
            'name'          => Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']),
            'criteria'      => Util_IDX::parseCriteria($criteria),
            'url'           => $url,
            'uid'           => $uid,
            'url_view'      => $url . 'idx/search.html?' . http_build_query(array_merge($criteria, array('uid' => $uid))),
            'url_save'      => $url . 'idx/search.html?' . http_build_query(array_merge($criteria, array('auto_save' => 1, 'uid' => $uid))),
            'signature'     => $signature,
            'unsubscribe'   => $unsubscribe
        ));

        // Generate Message
        $this->message  = '<p>Hello {name},</p>' . PHP_EOL;
        $this->message .= '<p>Thank you for using <a href="{url}?uid={uid}">{url}</a> for your real estate search!<p>' . PHP_EOL;
        $this->message .= '<p>Did you know that you can save your search parameters? <b>Save this Search</b> is a time saving feature that makes it easier than ever before to find homes that meet your needs. Save this Search eliminates the need to fill out a search form each and every time you\'d like to view listings, as it not only saves your customized search preferences, but you\'ll also be notified by email as soon as new properties enter the market that match your criteria.</p>' . PHP_EOL;
        $this->message .= '<p>Based on your previous searches and properties you\'ve chosen to view in detail, it appears that you\'re looking for homes with the following attributes:</p>';
        $this->message .= '<p>{criteria}</p>' . PHP_EOL;
        $this->message .= '<p>If you would like to be notified when new properties with these characteristics are listed on the ' . Lang::write('MLS') . ', simply click <a href="{url_save}">Save this Search</a>! You can also <a href="{url_view}">Refine</a> this search to meet your exact needs!</p>' . PHP_EOL;

        // Append Signature
        if (!empty($signature) && !empty($this->append)) {
            $this->message .= '<p>{signature}</p>' . PHP_EOL;
        }

        // Unsubscribe Link
        if (!empty($unsubscribe)) {
            $this->message .= '<p>Click here to unsubscribe: <a href="{unsubscribe}">{unsubscribe}</a></p>' . PHP_EOL;
        }

        // Return Message
        return $this->message;
    }
}
