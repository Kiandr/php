<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Phone is an abstract class that extends History_Event. This class is used to manage Agent phone calls made to leads.
 *
 * @package History
 */
abstract class History_Event_Phone extends History_Event implements History_IEditable
{

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

        // Save as Usual
        if (parent::save($db)) {
            // Last Call Data
            $data = array('timestamp' => $this->getTimestamp(), 'type' => preg_replace("/(([a-z])([A-Z])|([A-Z])([A-Z][a-z]))/", "\\2\\4 \\3\\5", $this->getSubtype()));

            // Increment `user`.`num_calls`
            foreach ($this->users as $user) {
                if ($user instanceof History_User_Lead) {
                    $db->query("UPDATE `users` SET `num_calls` = `num_calls` + 1, `last_call` = '" . json_encode($data) . "' WHERE `id` = '" . $user->getUser() . "';");
                }
            }

            // Return Event ID
            return $this->id;
        }

        // Return False
        return false;
    }

    /**
     * Get Call Details for Display
     * @see History_IExpandable::getDetails()
     */
    public function getDetails()
    {
        $message = htmlspecialchars($this->getData('details'));
        $message = nl2br($message);
        return $message;
    }

    /**
     * Get Editable Call Data
     * @see History_IEditable::getEditable()
     */
    public function getEditable()
    {
        return array('details');
    }

    /**
     * Get Form to Edit Call Details
     * @see History_IEditable::getEditForm()
     */
    public function getEditForm()
    {
        return '<textarea name="details" required>' . htmlspecialchars($this->getData('details')) . '</textarea>';
    }

    /**
     * Validation for Required Call Details
     * @see History_Event::edit()
     */
    public function edit(array $data, &$errors = array(), DBInterface $db = null)
    {
        // Require Details
        $data['details'] = trim($data['details']);
        if (empty($data['details'])) {
            $errors[] = 'Please supply call details.';
            return false;
        } else {
            return parent::edit($data, $errors, $db);
        }
    }
}
