<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Email is an abstract class that extends History_Event. This class is used to track outgoing emails.
 *
 * @package History
 */
abstract class History_Event_Email extends History_Event implements History_IExpandable
{

    /**
     * Get Event Details
     *
     * @return string Email Message
     */
    public function getDetails()
    {
        // Load email record
        $message = $this->loadNormalData();
        if (!empty($message)) {
            // Find & Replace Tags
            $tags = $this->getData('tags');
            if (!empty($tags) && is_array($tags)) {
                foreach ($tags as $tag => $value) {
                    $message = str_replace('{' . $tag . '}', $value, $message);
                }
            }
        } else {
            $message = $this->getData('message');
        }
        // Convert new lines to html line breaks (if Plaintext)
        if ($this->getData('plaintext')) {
            $message = nl2br($message);
        }
        // Return email message
        return $message;
    }

    /**
     * Save Event Data
     *
     * @param DBInterface $db Database connection
     * @return int Saved Event ID
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {

        // Get DB Connection
        $db = is_null($db) ? $this->db : $db;

        // Strip Emojis as the cause DB entry issues, and rendering issues on load
        $dataArray = $this->getData();
        foreach ($dataArray as $key => &$data) {
            if(in_array($key, ['message', 'subject'])) {
                $emojiStripper = new Format_Emoji();
                $data = $emojiStripper->strip($data);
            }
        }

        $this->setData($dataArray);

        // Save as Usual
        if (parent::save($db)) {
            // Last Email Data
            $data = array('timestamp' => $this->getTimestamp(), 'type' => preg_replace("/(([a-z])([A-Z])|([A-Z])([A-Z][a-z]))/", "\\2\\4 \\3\\5", $this->getSubtype()));

            // Increment `user`.`num_emails`
            foreach ($this->users as $user) {
                if ($user instanceof History_User_Lead) {
                    $db->query("UPDATE `users` SET `num_emails` = `num_emails` + 1, `last_email` = '" . json_encode($data) . "' WHERE `id` = '" . $user->getUser() . "';");
                }
            }

            // Return Event ID
            return $this->id;
        }

        // Return False
        return false;
    }

    /**
     * Store Email Message as Blob
     * @return string|NULL
     */
    public function getNormalDataToSave()
    {

        // Store email message in separate record
        if (!empty($this->data['tags'])) {
            // Remove message from data
            $message = $this->data['message'];
            unset($this->data['message']);

            // Return message
            return $message;
        }

        return null;
    }

    /**
     * @param string|array $data
     * @return string|array
     */
    protected function stripEmojis($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'stripEmojis'], $data);
        }
        $emojiStripper = new Format_Emoji();
        return $emojiStripper->strip($data);
    }
}
