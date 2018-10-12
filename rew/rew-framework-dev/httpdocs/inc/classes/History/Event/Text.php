<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Text is used to track SMS history of incoming/outgoing text messages
 *
 * @package History
 */
abstract class History_Event_Text extends History_Event implements History_IExpandable
{

    /**
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {
        $db = is_null($db) ? $this->db : $db;
        if (parent::save($db)) {
            // Last sent message
            $data = array('timestamp' => $this->getTimestamp(), 'type' => preg_replace("/(([a-z])([A-Z])|([A-Z])([A-Z][a-z]))/", "\\2\\4 \\3\\5", $this->getSubtype()));

            // Increment `user`.`num_texts`
            foreach ($this->getLeads() as $lead) {
                // Outgoign text message
                if ($this instanceof History_Event_Text_Outgoing) {
                    $query = $db->prepare("UPDATE `users` SET `num_texts` = `num_texts` + 1, `num_texts_outgoing` = `num_texts_outgoing` + 1, `last_text` = :last_text WHERE `id` = :id;");
                    $query->execute(array('last_text' => json_encode($data), 'id' => $lead->getUser()));

                // Incoming text message
                } else if ($this instanceof History_Event_Text_Incoming) {
                    $query = $db->prepare("UPDATE `users` SET `num_texts` = `num_texts` + 1, `num_texts_incoming` = `num_texts_incoming` + 1, `last_text` = :last_text WHERE `id` = :id;");
                    $query->execute(array('last_text' => json_encode($data), 'id' => $lead->getUser()));
                }
            }

            // Return Event ID
            return $this->id;
        }
        return false;
    }

    /**
     * Get SMS message's details (used for Incoming/Outout events)
     */
    public function getDetails()
    {
        $html = '';
        // Message body
        $body = $this->getBody();
        if (!empty($body)) {
            $html .= '<span class="sms">' . nl2br($body) . '</span>';
        }
        // Attached media
        $media = $this->getData('media');
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
     * Get text message body
     * @return string
     */
    protected function getBody()
    {
        $body = $this->getData('body');
        if (empty($body)) {
            return null;
        }
        return Format::htmlspecialchars($body);
    }

    /**
     * Format history message
     * @param string $message
     */
    protected function formatMessage($message)
    {

        // Event users
        $agent = $this->getAgent();
        $lead = $this->getLead();

        // Return formatted string
        return str_replace(array(
            ':to',
            ':from',
            ':agent',
            ':lead'
        ), array(
            $this->getTo(),
            $this->getFrom(),
            ($agent ? $agent->displayLink() : null),
            ($lead ? $lead->displayLink() : null)
        ), $message);
    }

    /**
     * Get involved agent
     * @return History_User|NULL
     */
    protected function getAgent()
    {
        foreach ($this->users as $user) {
            if (in_array($user->getType(), array(
                $user::TYPE_AGENT,
                $user::TYPE_ASSOCIATE,
                $user::TYPE_LENDER
            ))) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Get involved lead
     * @return History_User_Lead|NULL
     */
    protected function getLead()
    {
        foreach ($this->users as $user) {
            if ($user->getType() === $user::TYPE_LEAD) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Get involved leads
     * @return History_User_Lead[]
     */
    protected function getLeads()
    {
        return array_filter($this->users, function ($user) {
            return $user->getType() === $user::TYPE_LEAD;
        });
    }

    /**
     * Return formatted "From" number
     * @return string
     */
    protected function getFrom()
    {
        return Format::phone($this->getData('from'));
    }

    /**
     * Return formatted "To" number(s)
     * @return string
     */
    protected function getTo()
    {
        $to = $this->getNumbers('to');
        return implode(' and ', $to);
    }

    /**
     * Return array of formatted phone numbers
     * @param string $type Number type: "to" or "from"
     * @return array
     */
    protected function getNumbers($type)
    {
        if (!in_array($type, array('to', 'from'))) {
            return array();
        }
        $numbers = $this->getData($type);
        if (is_string($numbers)) {
            $numbers = array($numbers);
        } elseif (!is_array($numbers)) {
            return array();
        }
        return array_map(array('Format', 'phone'), $numbers);
    }
}
